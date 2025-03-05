<?php

namespace App\Services;

use app\Models\Inventory;
use App\Models\Treatment;
//use App\Models\InventoryUsage;

use App\Models\SKU;
use App\Models\SkuUsage;


use Exception;
use stdClass;

class InventoryService
{

    public static function stockCheckOnUpdate($new_treatment)
    {



        $original_treatment_details = Treatment::where('id', $new_treatment->id)->with('inventories:id,stock')->first();
        $original_quantity = $original_treatment_details->quantity;
        $new_quantity = $new_treatment->quantity;

        $stocks_before_update = [];
        $updating_stocks = [];
        foreach ($original_treatment_details->inventories as $inventory) {

            $units = new stdClass();
            $units->id = $inventory->id;
            $units->units = $inventory->pivot['units'] * $original_quantity + $inventory->stock;
            array_push($stocks_before_update, $units);
        };

        foreach ($new_treatment->inventories as $inventory) {

            $units = new stdClass();
            $units->id = $inventory['id'];
            $units->units = $inventory['pivot']['units'] * $new_quantity;
            $units->name = $inventory['name'];

            array_push($updating_stocks, $units);
        }
        $stocks_before_update_collection = collect($stocks_before_update);

        foreach ($updating_stocks as $updating_stock) {
            if ($stocks_before_update_collection->contains('id', $updating_stock->id)) {
                $stock = $stocks_before_update_collection->where('id', $updating_stock->id)->first();
                if ($stock->units >= $updating_stock->units) {
                    continue;
                } else {
                    return $updating_stock->name;
                }
            }
        }

        return true;
    }

    public static function refundStocks($id)
    {
        try {
            $treatment_details = Treatment::select('id', 'quantity',)->where('id', $id)->with('inventories')->first();
            $quantity = $treatment_details->quantity;

            $inventories = $treatment_details->inventories;
            foreach ($inventories as $inventory) {
                Inventory::select('id', 'stock')->where('id', $inventory->id)->increment('stock', $inventory['pivot']['units'] * $quantity);
            }
            return true;
        } catch (Exception $exception) {
            return $exception;
        }
    }


    //get requested stocks 
    public static function getStocks($id)
    {

        $used_units = 0;

        //get all skus of the inventory
        //$skus = SKU::select('id', 'units', 'name', 'description')->where('inventory_id', $id)->get();

        //get all sku_ids of the inventory
        $sku_ids = SKU::select('id')->where('inventory_id', $id)->get();

        //get all used units of each sku
        $usages = $sku_ids->each(function ($sku_id) use (&$used_units) {
            $used_units += SkuUsage::select('used_units')->where('sku_id', $sku_id->id)->sum('used_units');
        });

        //get all stocks of the inventory
        $stocks = SKU::select('units')->where('inventory_id', $id)->sum('units');

        //get remaining stocks
        $remaining_stocks = $stocks - $used_units;

        return $remaining_stocks;




        //return $stock;
    }
}
