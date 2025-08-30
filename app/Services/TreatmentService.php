<?php

namespace App\Services;

//use \illuminate\Support\Facades\DB;

use App\Models\Treatment;
use App\Models\Inventory;
use App\Models\TreatmentDetails;
use App\Models\Category;
use App\Models\SkuUsage;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\TreatmentDetailsRequest;
use DB;

class TreatmentService
{

    public static function addTreatment(TreatmentDetailsRequest $treatment_details)
    {



        /**
         * TODO: check if the sku belongs to the inventory
         */


        // check stocks availability
        $stocks = InventoryService::checkStocks($treatment_details->service_id, $treatment_details->sku_usage, $treatment_details->quantity);

        // return $stocks;
        /* if ($treatment_details->service_id == 2) {
            return $stocks;
        }
 */
        if ($stocks->enough_stocks != true) {
            $inssuficient_stocks = collect($stocks->stock_check_details)->where('enough_stocks', false);
            return response()->json([
                'message' => 'The following inventories does not have enough stocks left!',
                'inssuficient_stocks' => $inssuficient_stocks,
            ], 410);
        }

        DB::beginTransaction();
        try {
            //add basic treatment details
            $treatment = new Treatment;
            $treatment->service_id = $treatment_details->service_id;
            $treatment->patient_id = $treatment_details->patient_id;
            //$treatment->inventory_id = $treatment_details->inventory_id;
            $treatment->user_id = $treatment_details->user_id;
            $treatment->quantity = $treatment_details->quantity;
            $treatment->discount = $treatment_details->discount;
            $treatment->date = $treatment_details->date;
            $treatment->save();

            //get id of the treatment just created
            $treatment_id = $treatment->id;



            //add skus used in the treatment
            foreach ($treatment_details->sku_usage as $sku) {
                $sku_usage = new SkuUsage;
                $sku_usage->sku_id = $sku['sku_id'];
                $sku_usage->treatment_id = $treatment_id;
                $sku_usage->description = $sku['description'];
                $sku_usage->usage_date = $treatment->date;
                $sku_usage->used_units = $sku['used_units'];

                $sku_usage->save();

                DB::table('inventory_treatment')->insert([
                    'inventory_id' => $sku['inventory_id'],
                    'treatment_id' => $treatment_id,
                    'sku_id' => $sku['sku_id'],
                ]);
            }



            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        return response()->json([
            'message' => 'Treatment added successfully!',

        ], 201);
    }

    public static function getPatientTreatments($patient_id)
    {

        $treatment_details = Treatment::where('patient_id', $patient_id)->with('inventories.skus')->get();

        return $treatment_details;
    }

    public static function processTreatmentDetails($patient_id)
    {
        $final_treatment_details = [];

        //group_concat() to concatenate and group by of treatments
        $treatments = TreatmentDetails::select(DB::raw('group_concat(id) as t_id'), 'treatment_id', DB::raw('group_concat(inventory_id) as treatments'), DB::raw('group_concat(units) as amount'), 'units', 'quantity', 'patient_id', 'created_at', 'updated_at')
            ->where('patient_id', $patient_id)
            ->groupBy('treatment_id')
            ->latest();


        foreach ($treatments as $index => $treatment) {

            $treatment_details_id = explode(',', $treatment->t_id);


            $final_details = [];
            foreach ($treatment_details_id as $index => $treatment_detail_id) {
                $treatment_details = TreatmentDetails::findOrFail($treatment_detail_id)->toArray();
                $inventory_details = Inventory::findOrFail($treatment_details['inventory_id'])->toArray();
                $inventory_category = Category::findOrFail($inventory_details['categories_id'])->toArray();

                $treatment = Treatment::findOrFail($treatment_details['treatment_id'])->toArray();
                $inventory_details['units'] = $treatment_details['units'];
                $inventory_details['treatment_id'] = $treatment_details['treatment_id'];
                $inventory_details['treatment_details_id'] = $treatment_details['id'];
                $inventory_details['patient_id'] = $treatment_details['patient_id'];
                $inventory_details['quantity'] = $treatment_details['quantity'];
                $inventory_details['patient_id'] = $treatment_details['patient_id'];
                $inventory_details['user_id'] = $treatment_details['user_id'];
                $inventory_details['categories_id'] = $inventory_details['categories_id'];
                $inventory_details['service_title'] = $inventory_category['categories'];
                $inventory_details['treatment_created_at'] = $treatment_details['created_at'];
                $inventory_details['treatment_updated_at'] = $treatment_details['updated_at'];
                $inventory_details['treatment_date'] = $treatment_details['date'];

                array_push($final_details, $inventory_details);
            }

            array_push($final_treatment_details, $final_details);
        }
        return $final_treatment_details;
    }

    public static function processSingleTreatment($id)
    {
        $treatment_details = TreatmentDetails::where('treatment_id', $id)->get();
        $discount = Treatment::select('discount')->find($id);
        $treatment = [];
        $final_detail = [];

        foreach ($treatment_details as $treatment_detail) {
            $treatment['inventory'] = Inventory::where('id', $treatment_detail['inventory_id'])->first();
            $treatment['units'] = $treatment_detail['units'];
            $treatment['quantity'] = $treatment_detail['quantity'];
            $treatment['discount'] = $treatment_detail['discount'];
            $treatment['categories_id'] = Inventory::select('categories_id')->where('id', $treatment_detail['inventory_id'])->value('categories_id');
            $treatment['discount'] = $discount->discount;
            array_push($final_detail, $treatment);
        }

        return $final_detail;
    }

    public static function processUpdateTreatment($treatment_id)
    {
        //TODO: Tighter database check might needed for more reliable update

        $original_treatment = Treatment::findOrFail($treatment_id);
        $original_treatment_details = TreatmentDetails::where('treatment_id', $treatment_id)->get();

        foreach ($original_treatment_details as $index => $original_treatment_detail) {

            //add inventory stocks back to prepare for updating treatment
            $treatment_units = $original_treatment_detail['units'] * $original_treatment_detail['quantity'];
            $inventory_stock = Inventory::findOrFail($original_treatment_detail['inventory_id']);
            $final_units = $inventory_stock->stock + $treatment_units;
            $inventory_stock->stock = $final_units;
            $inventory_stock->save();

            //TODO: Finacial features neeeded here

            //Delete treatment details to prepare for update.            
        };

        $treatment_delete = TreatmentDetails::where('treatment_id', $treatment_id)->delete();

        return $treatment_delete;
    }
}
