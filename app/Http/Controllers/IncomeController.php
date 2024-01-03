<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Income;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $income = Income::all();
        return response()->json([
            'data'=> $income,
            'code'=>200
        ]);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function addIncomeByService(Request $request)
    {
        $this->validate($request, array(
            'service_id' => 'required|integer|min:0',
            'amount' => 'required',
            'discount' => 'required',
            'quantity' => 'required',
            'user_id' => 'required',
            'income_type_id' => 'required',
        ));

        $income = new Income;
        $income->amount = $request->amount;
        $income->service_id = $request->service_id;
        $income->discount = $request->discount;
        $income->quantity = $request->quantity;
        $income->income_type_id = $request->income_type_id;
        $income->user_id = $request->user_id;

        $income->save();

        return response()->json([
            'data'=> 'Income has been added!',
            'code'=>200
        ]);
    }

    public function addIncomeByHerbs(Request $request)
    {
        return null;
    }

    public function addIncomeByRetail(Request $request)
    {
        return null;
    }

    public function addIncomeByOther(Request $request)
    {
        return null;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
