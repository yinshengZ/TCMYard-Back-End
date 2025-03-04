<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SkuUsage;

class SkuUsageController extends Controller

{

    public function index()
    {
        $sku_usage = SkuUsage::all();
        return response()->json([
            'data' => $sku_usage,
            'code' => 200
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, array(
            'sku_id' => 'required|integer',
            'description' => 'string',
            'usage_date' => 'required|date',
            'used_units' => 'required|integer'
        ));

        $sku_usage = new SkuUsage;
        $sku_usage->sku_id = $request->sku_id;
        $sku_usage->description = $request->description;
        $sku_usage->usage_date = $request->usage_date;
        $sku_usage->used_units = $request->used_units;

        $sku_usage->save();

        return response()->json([
            'data' => 'sku usage has been added!',
            'code' => 200
        ]);
    }

    public function show($id)
    {
        $sku_usage = SkuUsage::find($id);
        return response()->json([
            'data' => $sku_usage,
            'code' => 200
        ]);
    }


    /**
     * Search for sku usage by sku id
     * @param  $sku_id
     * @return collection of sku usage 
     */
    public function search_by_sku_id($sku_id)
    {
        $sku_usage = SkuUsage::where('sku_id', $sku_id)->get();
        return response()->json([
            'data' => $sku_usage,
            'code' => 200
        ]);
    }

    public function delete($id)
    {
        $sku_usage = SkuUsage::find($id);
        $sku_usage->delete();
        return response()->json([
            'data' => 'sku usage has been deleted!',
            'code' => 200
        ]);
    }
}
