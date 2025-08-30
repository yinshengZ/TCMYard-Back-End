<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Requests\TreatmentUpdateRequest;

use App\Services\InventoryService;
use App\Services\FinanceService;
use App\Services\TreatmentService;

use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Inventory;
use App\Models\Treatment;
use App\Models\Income;
use App\Models\TreatmentDetails;
use App\Models\InventoryUsage;



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

        $treatments = TreatmentService::getPatientTreatment($id);
        return $treatments;

        /* 
        $treatments = Treatment::where('patient_id', $id)
            ->with('category')
            ->with('inventories')
            ->with('incomes')
            ->orderBy('date', 'DESC')
            ->get();
        return response()->json([
            'data' => $treatments,
            'code' => 200
        ]); */
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
            $treatment->income = $incomes->where('service_id', $treatment->service_id)->first();
        }


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

        return $request;
        //stocks check
        $herbs = $request->herb_details;
        foreach ($herbs as $herb) {
            //$stock = Inventory::select('stock', 'id', 'name')->where('id', $herb['id'])->first();

            /* if ($stock->stock < $herb['units'] * $request->quantity) {
                return response()->json([
                    'data' => "$stock->name" . ' does not have enough stocks left!',
                    'code' => 90001
                ]);
            } */


            //FIXME: incorrect queries sent to the server. needs debug 

            //TODO: check if $herb->name exsists
            $current_stock = InventoryService::getStocks($herb['id']);
            $stock = $current_stock - $herb['units'] * $request->quantity;
            return "hello error";
            /*  if ($stock <= 0) {
                return response()->json([
                    'data' => "$herb->name" . ' does not have enough stocks left!',
                    'code' => 90001
                ]);
            } */
        }


        try {
            DB::transaction(function () use ($request) {
                $herb_ids = [];
                $herb_units = [];


                //construct (id, units) array pairs for attach() with many to many relationship
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


                // decrement() stocks
                $herb_details = $request->herb_details;
                foreach ($herb_details as $herb_detail) {
                    Inventory::where('id', $herb_detail['id'])->decrement('stock', $herb_units[$key] * $request->quantity);
                }


                //add income
                if ($request->with_finance) {
                    $treatment_id = Treatment::select('id')->where('user_id', '=', $request->user_id)
                        ->where('patient_id', '=', $request->patient_id)
                        ->where('service_id', '=', $request->service_id)
                        ->latest()
                        ->first();

                    $income = new Income;

                    $income->amount = $request->final_price * 100;

                    $income->original_amount = $request->original_price * 100;
                    //$income->treatment_id = $treatment_ids['id'];
                    $income->treatment_id = $treatment_id->id;
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
        } catch (Exception $error) {

            echo 'message: ' . $error->getMessage();
        }
    }

    /**
     * @param $request
     */

    public function addServices(Request $request)
    {
        $request->validate([
            'quantity' => 'required|Numeric|min:1',
            'patient_id' => 'required',
            'service_id' => 'required',
            'user_id' => 'required',
        ]);
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

        $request->validate([
            'quantity' => 'required|Numeric|min:1',
            'patient_id' => 'required',
            'service_id' => 'required',
            'user_id' => 'required',
        ]);


        //check retail stocks
        $retails = $request->retail_details;

        foreach ($retails as $retail) {
            $stock = Inventory::select('stock', 'id', 'name')
                ->where('id', $retail['id'], $retail['id'])
                ->first();
            if ($stock->stock < $retail['units']) {
                return response()->json([
                    'data' => "$retail->name" . ' does not have enough stocks left!',
                    'code' => 90001
                ]);
            }
        }


        DB::transaction(function () use ($request) {
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
        });


        return response()->json([
            'data' => 'Treatment has been added!',
            'code' => 200
        ]);
    }

    public function addOther(Request $request)
    {


        $request->validate([
            'quantity' => 'required|Numeric|min:1',
            'patient_id' => 'required',
            'service_id' => 'required',
            'user_id' => 'required',
        ]);

        //check others stocks
        $others = $request->others_details;

        foreach ($others as $other) {
            $stock = Inventory::select('stock', 'id', 'name')
                ->where('id', $other['id'])
                ->first();

            if ($stock->stock < $other['units']) {
                return response()->json([
                    'data' => "$stock->name" . ' does not have enough stocks left!',
                    'code' => 90001
                ]);
            };
        }

        DB::transaction(function () use ($request) {
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
        });



        return response()->json([
            'data' => 'Treatment has been added!',
            'code' => 200
        ]);
    }

    /**
     * function to update herbs treatment.
     * 1: check if there are enough stocks left with refunding of previous recorded usage.(check)
     * 2: refund stocks to inventories if 1. had passed
     * 3: update treatment details
     * 3.1: if income needs to be updated, update income
     * 3.2: if date needs to be updated, update date
     * @param $request
     */

    public function updateHerb(TreatmentUpdateRequest $request)
    {


        //check if there are enough stocks left with refunding before update
        $stock = InventoryService::stockCheckOnUpdate($request);
        if ($stock != 1) {
            return response()->json([
                'data' => "$stock" . ' will not have enough stocks left!',
                'code' => 90001
            ]);
        }
        //refund stocks before updating
        $refundStocks = InventoryService::refundStocks($request->id);
        if ($refundStocks != true) {
            return response()->json([
                'data' => 'Inventory refund was not successful!',
                'code' => 90002
            ]);
        }

        try {
            DB::transaction(function () use ($request) {
                $herb_ids = [];
                $herb_units = [];

                //reconstructing inventory details for sync()
                //sync() required format [1=>['units']=>10]
                foreach ($request->inventories as $key => $herb_detail) {
                    array_push($herb_ids, $herb_detail['id']);
                    array_push($herb_units, $herb_detail['pivot']['units']);
                }

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

                //update inventories stock;
                foreach ($request->inventories as $inventory) {
                    Inventory::where('id', $inventory['id'])->decrement('stock', $inventory['pivot']['units'] * $request->quantity);
                };

                $treatment->inventories()->sync($updated_inventories);

                $treatment->save();
            });
            return response()->json([
                'data' => 'Treatment has been updated!',
                'code' => 200
            ]);
        } catch (Exception $exception) {
            return $exception;
        };
    }

    public function updateRetail(TreatmentUpdateRequest $request)
    {

        DB::transaction(function () use ($request) {
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
        });



        return response()->json([
            'data' => 'Treatment has been updated!',
            'code' => 200
        ]);
    }


    public function updateService(TreatmentUpdateRequest $request)
    {
        DB::transaction(function () use ($request) {
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
        });


        return response()->json([
            'data' => 'Treatment has been updated!',
            'code' => 200
        ]);
    }

    public function updateOther(TreatmentUpdateRequest $request)
    {

        DB::transaction(function () use ($request) {
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
        });


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
        //TODO: Add decremented stocks back
        InventoryService::refundStocks($id);
        $treatment = Treatment::where('id', $id)->delete();
        $income = Income::where('treatment_id', $id)->delete();

        return response()->json([
            'data' => 'Treatment has been deleted!',
            'code' => 200
        ]);
    }
}
