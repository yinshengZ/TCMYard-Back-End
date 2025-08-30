<?php

namespace App\Http\Controllers;

use App\Models\InventoryUsage;

use Illuminate\Http\Request;

use App\Services\InventoryService;

class InventoryUsageController extends Controller
{
    public function index()
    {
        $inventoy_usages = InventoryUsage::all();
        return response()->json([
            'data' => $inventoy_usages,
            'code' => 200
        ]);
    }

    public function show($id)
    {
        $stock = InventoryService::getStocks($id);
        return $stock;

        /* $inventory_usage = InventoryUsage::where('id', $id)->first();

        return response()->json([
            'data' => $inventory_usage,
            'code' => 200
        ]); */
    }

    public function store(Request $request)
    {
        /* if ($request->restock) {
            $inventory_usage = new InventoryUsage;
            $inventory_usage->in = $request->usage;
            $inventory_usage->inventory_id = $request->id;
            $inventory_usage->user_id = $request->user_id;
            $inventory_usage->description = $request->description;
            $inventory_usage->date = $request->date;
            $inventory_usage->save();

            return response()->json([
                'data' => 'Stock has been added!',
                'code' => 200
            ]);
        } */

        //TODO: add function for stock usages;
    }

    public function update(Request $request) {}

    public function delete($id) {}
}
