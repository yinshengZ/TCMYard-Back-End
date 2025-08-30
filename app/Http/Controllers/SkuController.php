<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SKU;

class SkuController extends Controller
{


    public function index()
    {
        $sku = SKU::all();
        return response()->json([
            'data' => $sku,
            'code' => 200
        ]);
    }

    public function show($sku_id)
    {
        $sku = SKU::find($sku_id);
        return response()->json([
            'data' => $sku,
            'code' => 200
        ]);
    }

    //Add inventory skus
    public function store(Request $request)
    {

        $this->validate($request, array(
            'inventory_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'string',
            'stocking_date' => 'required|date',
            'expiry_date' => 'required|date',
            'units' => 'required|integer',
            'out_of_stock' => 'required|boolean'
        ));

        $sku = new SKU;
        $sku->inventory_id = $request->inventory_id;
        $sku->name = $request->name;
        $sku->description = $request->description;
        $sku->stocking_date = $request->stocking_date;
        $sku->expiry_date = $request->expiry_date;
        $sku->units = $request->units;
        $sku->out_of_stock = $request->out_of_stock;


        $sku->save();

        return response()->json([
            'data' => 'sku has been added!',
            'code' => 200
        ]);
    }

    //update sku details
    public function update(Request $request)
    {
        $this->validate($request, array(
            'sku_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'string',
            'inventory_id' => 'required|integer',
            'stocking_date' => 'required|date',
            'expiry_date' => 'required|date',
            'units' => 'required|integer',
            'out_of_stock' => 'required|boolean'
        ));
        $sku = SKU::find($request->sku_id);
        $sku->name = $request->name;
        $sku->description = $request->description;
        $sku->inventory_id = $request->inventory_id;
        $sku->stocking_date = $request->stocking_date;
        $sku->expiry_date = $request->expiry_date;
        $sku->units = $request->units;
        $sku->out_of_stock = $request->out_of_stock;

        $sku->save();
        return response()->json([
            'data' => 'sku has been updated!',
            'code' => 200
        ]);
    }

    //delete sku
    public function destroy($id)
    {
        $sku = SKU::find($id);
        $sku->delete();
        return response()->json([
            'data' => 'sku has been deleted!',
            'code' => 200
        ]);
    }
}
