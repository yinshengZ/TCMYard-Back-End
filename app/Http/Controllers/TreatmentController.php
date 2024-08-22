<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Requests\TreatmentUpdateRequest;

use Exception;
use DB;
use Carbon\Carbon;

use App\Models\Inventory;
use App\Models\Treatment;
use App\Models\Income;
use App\Models\TreatmentDetails;


class TreatmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $treatments = Treatment::all()->with('category');
        return response()->json([
            'data' => $treatments,
            'code' => 200
        ]);
    }

    //get single treatment data for displaying or updating
    public function show($id)
    {
        // $treatment_details = TreatmentService::processSingleTreatment($id);

        $treatment_details = Treatment::where('id', $id)->with('category')->with('inventories')->with('incomes')->first();

        return response()->json([
            'data' => $treatment_details,
            'code' => 200
        ]);
    }



    public function getPatientTreatments($id)
    {
        $treatments = Treatment::where('patient_id', $id)->with('category')->with('inventories')->with('incomes')->orderBy('date', 'DESC')->get();
        return response()->json([
            'data' => $treatments,
            'code' => 200
        ]);
    }

    public function getPatientTreatmentsDistribution($id)
    {
        $treatments = Treatment::select('id', 'service_id', DB::raw('count(service_id) as count'))
            ->where('patient_id', $id)
            ->groupBy('service_id')
            ->with('category:id,categories')
            ->get();

        $incomes = Income::select('service_id', DB::raw('sum(amount) as total'))
            ->where('patient_id', $id)
            ->groupBy('service_id')
            ->get();

        foreach ($treatments as $index => $treatment) {
            /* $treatment->merge($incomes->where('service_id', $treatment->service_id)); */
            $treatment->income = $incomes->where('service_id', $treatment->service_id)->first();
        }

        /* return $incomes; */
        return response()->json([
            'data' => $treatments,
            'code' => 200,
        ]);
    }

    public function addHerbalPackages(Request $request)
    {


        $request->validate([
            'quantity' => 'required|Numeric|min:1',
            'patient_id' => 'required',
            'service_id' => 'required',
            'user_id' => 'required',
        ]);

        //Check if there are enough stocks left before the transaction begins
        $herbs = $request->herb_details;

        foreach ($herbs as $herb) {

            $stock = Inventory::select('stock', 'id', 'name')->where('id', $herb['id'])->first();
            if ($stock->stock < $herb['units']) {
                return response()->json([
                    'data' => "$stock->name" . ' does not have enough stocks left!',
                    'code' => 90001
                ]);
            }
        }


        DB::transaction(function () use ($request) {
            $herb_ids = [];
            $herb_units = [];



            foreach ($request->herb_details as $key => $herb_detail) {
                array_push($herb_ids, $herb_detail['id']);
                array_push($herb_units, $herb_detail['units']);
            }

            $treatment = new Treatment;
            $treatment->service_id = $request->service_id;
            $treatment->patient_id = $request->patient_id;
            $treatment->user_id = $request->user_id;
            $treatment->quantity = $request->quantity;
            if (is_null($request->discount)) {
                $treatment->discount = 0;
            } else {
                $treatment->discount = $request->discount;
            }
            $treatment->discount = $request->discount;
            if ($request->with_date) {
                $treatment->date = $request->date;
            } else {
                $treatment->date = Carbon::today();
            }

            $treatment->save();
            foreach ($herb_ids as $key => $herb_id) {
                $treatment->inventories()->attach($herb_ids[$key], ['units' => $herb_units[$key]]);
            }

            if ($request->with_finance) {

                $income = new Income;

                $income->amount = $request->final_price * 100;

                $income->original_amount = $request->original_price * 100;
                //$income->treatment_id = $treatment_ids['id'];
                $income->patient_id = $request->patient_id;
                $income->user_id = $request->user_id;
                $income->discount = $request->discount;

                $income->discount = $request->discount;
                if ($request->with_date) {
                    $income->date = $request->date;
                } else {
                    $income->date = Carbon::today();
                }
                $income->service_id = $request->service_id;
                $income->payment_type_id = $request->payment_type;
                $income->description = $request->description;
                $treatment->incomes()->save($income);
            }
        });


        return response()->json([
            'data' => 'Treatment has been added!',
            'code' => 200
        ]);
    }

    /**
     * @param $request
     */

    public function addServices(Request $request)
    {
        DB::transaction(function () use ($request) {
            $service = new Treatment;
            $service->service_id = $request->service_id;
            $service->patient_id = $request->patient_id;
            $service->user_id = $request->user_id;
            $service->quantity = $request->quantity;
            if ($request->with_date) {
                $service->date = $request->date;
            } else {
                $service->date = Carbon::today();
            }
            $service->discount = $request->discount;
            $service->save();

            $service->inventories()->attach($request->id, ['units' => $request->unit]);

            if ($request->with_finance) {
                $income = new Income;

                $income->amount = $request->final_price * 100;
                $income->original_amount = $request->original_price * 100;
                $income->payment_type_id = $request->payment_type_id;
                $income->patient_id = $request->patient_id;
                $income->user_id = $request->user_id;
                $income->service_id = $request->service_id;
                $income->discount = $request->discount;
                $income->description = $request->description;

                if ($request->with_date) {
                    $income->date = $request->date;
                } else {
                    $income->date = Carbon::today();
                }

                $service->incomes()->save($income);
            }
        });


        return response()->json([
            'data' => 'Treatment has been added!',
            'code' => 200
        ]);
    }
    /**
     * @param $request
     * recives the retail treatment details from the front end and store them into the database.
     * 
     */

    public function addRetail(Request $request)
    {
        //TODO: add check stocks before adding!
        $retail_ids = [];
        $retail_units = [];


        foreach ($request->retail_details as $key => $retail_detail) {
            array_push($retail_ids, $retail_detail['id']);
            array_push($retail_units, $retail_detail['units']);
        }


        $treatment = new Treatment;
        $treatment->service_id = $request->service_id;
        $treatment->patient_id = $request->patient_id;
        $treatment->user_id = $request->user_id;
        $treatment->quantity = 1;
        if ($request->with_date) {
            $treatment->date = $request->date;
        }

        $treatment->save();

        foreach ($retail_ids as $key => $retail_id) {
            $treatment->inventories()->attach($retail_ids[$key], ['units' => $retail_units[$key]]);
        }

        if ($request->with_finance) {
            $income = new Income;

            $income->amount = $request->final_price * 100;
            $income->original_amount = $request->original_price * 100;
            $income->payment_type_id = $request->payment_type_id;
            $income->patient_id = $request->patient_id;
            $income->user_id = $request->user_id;
            $income->service_id = $request->service_id;
            $income->discount = $request->discount;
            $income->description = $request->description;

            if ($request->with_date) {
                $income->date = $request->date;
            } else {
                $income->date = Carbon::today();
            }
            $treatment->incomes()->save($income);
        }

        return response()->json([
            'data' => 'Treatment has been added!',
            'code' => 200
        ]);
    }

    public function addOther(Request $request)
    {
        $others_ids = [];
        $others_units = [];

        foreach ($request->others_details as $key => $others_detail) {
            array_push($others_ids, $others_detail['id']);
            array_push($others_units, $others_detail['units']);
        }

        $treatment = new Treatment;
        $treatment->service_id = $request->service_id;
        $treatment->patient_id = $request->patient_id;
        $treatment->user_id = $request->user_id;
        $treatment->quantity = 1;
        if ($request->with_date) {
            $treatment->date = $request->date;
        }
        $treatment->save();

        foreach ($others_ids as $key => $others_id) {
            $treatment->inventories()->attach($others_ids[$key], ['units' => $others_units[$key]]);
        };

        if ($request->with_finance) {
            $income = new Income;
            $income->amount = $request->final_price * 100;
            $income->original_amount = $request->original_price * 100;
            $income->payment_type_id = $request->payment_type;
            $income->patient_id = $request->patient_id;
            $income->user_id = $request->user_id;
            $income->service_id = $request->service_id;
            $income->discount = $request->discount;
            $income->description = $request->description;

            if ($request->with_date) {
                $income->date = $request->date;
            } else {
                $income->date = Carbon::today();
            }
            $treatment->incomes()->save($income);
        }

        return response()->json([
            'data' => 'Treatment has been added!',
            'code' => 200
        ]);
    }


    public function updateHerb(TreatmentUpdateRequest $request)
    {
        $herb_ids = [];
        $herb_units = [];




        foreach ($request->inventories as $key => $herb_detail) {
            array_push($herb_ids, $herb_detail['id']);
            array_push($herb_units, $herb_detail['pivot']['units']);
        }
        //reconstructing inventory details for sync()
        //sync() required format [1=>['units']=>10]
        $units = array_map(function ($units) {
            return ['units' => $units];
        }, $herb_units);

        $updated_inventories = array_combine($herb_ids, $units);


        $treatment = Treatment::findOrFail($request->id);
        $treatment->quantity = $request->quantity;
        if ($request->with_date) {
            $treatment->date = $request->date;
        }

        if ($request->with_finance) {
            $income =  $request->incomes[0];

            $income['amount'] = $income['amount'] * 100;
            $income['original_amount'] = $income['original_amount'] * 100;
            $treatment->incomes()->update($income);
        }

        $treatment->inventories()->sync($updated_inventories);

        $treatment->save();

        return response()->json([
            'data' => 'Treatment has been updated!',
            'code' => 200
        ]);
    }

    public function updateRetail(TreatmentUpdateRequest $request)
    {

        $retail_ids = [];
        $retail_units = [];



        foreach ($request->inventories as $key => $retail_detail) {
            array_push($retail_ids, $retail_detail['id']);
            array_push($retail_units, $retail_detail['pivot']['units']);
        }

        $units = array_map(function ($units) {
            return ['units' => $units];
        }, $retail_units);

        $updated_inventories = array_combine($retail_ids, $units);

        $retail = Treatment::findOrFail($request->id);
        $retail->quantity = $request->quantity;
        if ($request->with_date) {
            $retail->date = $request->date;
        }

        if ($request->with_finance) {

            $income = $request->incomes[0];

            $income['amount'] = $income['amount'] * 100;
            $income['original_amount'] = $income['original_amount'] * 100;
            $retail->incomes()->update($income);
        }

        $retail->inventories()->sync($updated_inventories);
        $retail->save();

        return response()->json([
            'data' => 'Treatment has been updated!',
            'code' => 200
        ]);
    }


    public function updateService(TreatmentUpdateRequest $request)
    {
        $inventory_id = $request->inventories[0]['pivot']['inventory_id'];


        $treatment = Treatment::findOrFail($request->id);
        $treatment->quantity = $request->quantity;
        if ($request->with_date) {
            $treatment->date = Carbon::parse($request->date);
        }

        if ($request->with_finance) {
            $income = $request->incomes[0];

            $income['amount'] = $income['amount'] * 100;
            $income['original_amount'] = $income['original_amount'] * 100;
            $treatment->incomes()->update($income);
        }

        $treatment->inventories()->sync([$inventory_id], ['units' => 1]);
        $treatment->save();

        return response()->json([
            'data' => 'Treatment has been updated!',
            'code' => 200
        ]);
    }

    public function updateOther(TreatmentUpdateRequest $request)
    {

        $other_ids = [];
        $other_units = [];

        foreach ($request->inventories as $key => $other_detail) {
            array_push($other_ids, $other_detail['pivot']['inventory_id']);
            array_push($other_units, $other_detail['pivot']['units']);
        }

        $units = array_map(function ($units) {
            return ['units' => $units];
        }, $other_units);


        $updated_inventories = array_combine($other_ids, $units);

        $other = Treatment::findOrFail($request->id);
        $other->quantity = $request->quantity;
        if ($request->with_date) {
            $other->date = $request->date;
        }

        if ($request->with_finance) {
            $income = $request->incomes[0];
            $income['amount'] = $income['amount'] * 100;
            $income['original_amount'] = $income['original_amount'] * 100;
            $other->incomes()->update($income);
        }

        $other->inventories()->sync($updated_inventories);
        $other->save();


        return response()->json([
            'data' => 'Treatment has been updated!',
            'code' => 200
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $treatment = Treatment::where('id', $id)->delete();
        $income = Income::where('treatment_id', $id)->delete();
        return response()->json([
            'data' => 'Treatment has been deleted!',
            'code' => 200
        ]);
    }
}
