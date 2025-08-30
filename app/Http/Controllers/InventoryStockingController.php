<?php

namespace App\Http\Controllers;

use App\Models\InventoryStocking;

use Illuminate\Http\Request;

class InventoryStockingController extends Controller
{
    public function index() {}

    //gather records of a paticular inventory's history
    public function show($id)
    {
        $stockings = InventoryStocking::where('inventory_id', $id)->get();
        return response()->json([
            'data' => $stockings,
            'code' => 200
        ]);
    }

    public function store(Request $request)
    {
        if ($request->stock <= 0) {
            return response()->json([
                'data' => 'Added stock cannot be 0 or lower!',
                'code' => 90001
            ]);
        } else {
            $inventory_stocking = new InventoryStocking;
            $inventory_stocking->inventory_id = $request->id;
            $inventory_stocking->user_id = $request->user_id;
            $inventory_stocking->stock = $request->stock;
            $inventory_stocking->description = $request->description;
            $inventory_stocking->date = $request->date;
            $inventory_stocking->save();

            return response()->json([
                'data' => 'Stock has been added!',
                'code' => 200
            ]);
        }
    }

    public function update(Request $request) {}

    public function destory($id) {}
}
