<?php

namespace App\Services;

use DB;

use App\Models\Treatment;
use App\Models\Inventory;
use App\Models\TreatmentDetails;
use App\Models\Category;
use Exception;

class TreatmentService
{

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
