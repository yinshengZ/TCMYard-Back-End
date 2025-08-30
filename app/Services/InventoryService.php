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

    /**
     * refund stocks
     * @param $treatment_id
     * @return bool
     * 
     */
    public static function refundStocks($treatment_id)
    {
       
        //TODO:: implement refundStocks
        $treatment_details = Treatment::select('id', 'quantity',)->where('id', $treatment_id)->with('inventories')->first();
    }

    public static function checkStocks($service_id, $stocks_to_check, $quantity)
    {
        //print_r($stocks_to_check);
        //int_r($stocks_to_check);




        //final array to return 
        $final_details = [];

        //stocks checked
        $final_stocks = new stdClass();

        $enough_stocks = true;

        if ($service_id == 2) {
            //have to assign the property first, cant return as $final_stocks->enough_stocks = true; it will return 1 without the object.
            $final_stocks->enough_stocks = $enough_stocks;
            return $final_stocks;
        } else {
            foreach ($stocks_to_check as $index => $stock) {
                $stock_check_details = new stdClass();
                $stock_id = $stock['sku_id'];

                $stock_units = self::getStocksBySkuId($stock_id);


                if ($stock_units->remaining_sku_stock <= 0 || $stock_units->remaining_sku_stock < $stock['used_units'] * $quantity) {
                    $stock_check_details->sku_id = $stock_id;
                    $stock_check_details->sku_name = $stock_units->sku_name;
                    $stock_check_details->sku_description = $stock_units->sku_description;
                    $stock_check_details->remaining_sku_stock = $stock_units->remaining_sku_stock;
                    $stock_check_details->enough_stocks = false;
                    $final_details[$index] = $stock_check_details;
                    $enough_stocks = false;
                } else {
                    $stock_check_details->sku_id = $stock_id;
                    $stock_check_details->sku_name = $stock_units->sku_name;
                    $stock_check_details->sku_description = $stock_units->sku_description;
                    $stock_check_details->remaining_sku_stock = $stock_units->remaining_sku_stock;
                    $stock_check_details->enough_stocks = true;
                    $final_details[$index] = $stock_check_details;
                }
            }


            $final_stocks->stock_check_details = $final_details;
            $final_stocks->enough_stocks = $enough_stocks;
            return $final_stocks;
        }
    }


    //get requested stocks 
    public static function getStocksByInventoryId($id)
    {


        $total_used_units = 0;
        $remaining_inventory_stock = 0;


        $stock_info = new stdClass();

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

        $stock_info->$remaining_inventory_stock = $remaining_stocks;

        return $remaining_inventory_stock;
    }


    public static function getStocksBySkuId($id)
    {

        $stock_details = new stdClass();
        $sku_name = '';
        $sku_description = '';

        //get all skus of the inventory
        $stocks = SKU::select('units', 'name', 'description')->where('id', $id)->first();


        $usages = SkuUsage::select('used_units')->where('sku_id', $id)->sum('used_units');

        $remaining_sku_stock = $stocks->units - doubleval($usages);
        if ($remaining_sku_stock < 0) {
            $remaining_sku_stock = 0;
        }
        $stock_details->sku_name = $stocks->name;
        $stock_details->sku_description = $stocks->description;
        $stock_details->remaining_sku_stock = $remaining_sku_stock;
        $stock_details->sku_id = $id;



        return $stock_details;
    }
}
