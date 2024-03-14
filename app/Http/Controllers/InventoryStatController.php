<?php

namespace App\Http\Controllers;

use DB;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Treatment;
use App\Services\Ultilities;
use illuminate\Support\Carbon;
use stdClass;

class InventoryStatController extends Controller
{
    public function get_expirying_inventories()
    {
        $expirying_inventory = Inventory::whereDate('expiry_date', '<', Carbon::now()->addDays(50))
            ->orderBy('expiry_date', 'ASC')
            ->get();

        return response()->json([
            'data' => $expirying_inventory,
            'code' => 200
        ]);
    }


    public function most_used_inventories($quantity, $year)
    {
        $inventory = DB::table('inventory_treatment')
            ->select(DB::raw('inventory_id, COUNT(inventory_id) as counts'))
            ->whereYear('updated_at', $year)
            ->groupBy('inventory_id')
            ->orderBy('counts', 'desc')
            ->take($quantity)
            ->get();

        foreach ($inventory as $index => $item) {
            $inventory_info[$index] = Inventory::select('id', 'name')->where('id', '=', $item->inventory_id)->get();
            /*             $inventory[$index]->put('inventory_info', $inventory_info);
 */
            $monthly_usage[$index] = $this->get_inventory_usage_counts($item->inventory_id, $year);
        }

        foreach ($inventory as $index => $item) {
            $inventory[$index]->inventory_info = $inventory_info[$index];
            $inventory[$index]->monthly_usage = $monthly_usage[$index];
        }



        return response()->json([
            'data' => $inventory,
            'code' => 200
        ]);
    }

    public function get_inventory_usage_counts($id, $year)
    {

        $inventory_count = DB::table('inventory_treatment')
            ->select(DB::raw('inventory_id,COUNT(inventory_id) as counts, Month(updated_at) as month'))
            ->where('inventory_id', '=', $id)
            ->whereYear('updated_at', $year)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();


        $months_num = Ultilities::getAllMonths('num');
        $final_data = [];

        for ($i = 0; $i < count($months_num); $i++) {
            $temp_data = new stdClass();
            foreach ($inventory_count as $key => $count) {

                if ($count->month == $months_num[$i]) {
                    $temp_data->inventory_id = (int)$id;
                    $temp_data->counts = $count->counts;
                    $temp_data->month = $months_num[$i];
                    break;
                }
            }
            $tmp = (array)$temp_data;
            if (empty($tmp)) {
                $temp_data->inventory_id = (int)$id;
                $temp_data->counts = 0;
                $temp_data->month = $months_num[$i];
                array_push($final_data, $temp_data);
            } else {
                array_push($final_data, $temp_data);
            }
        }
        return $final_data;

        /* return response()->json([
            'data' => $final_data,
            'code' => 200
        ]); */
    }

    public function get_all_inventory_recorded_years()
    {
        $years = DB::table('inventory_treatment')
            ->select(DB::raw('inventory_id, Year(updated_at) as year'))
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->get();
        return response()->json([
            'data' => $years,
            'code' => 200
        ]);
    }

    public function get_inventory_used_years($id)
    {
        $years  = DB::table('inventory_treatment')
            ->select(DB::raw('inventory_id, Year(updated_at) as year'))
            ->where('inventory_id', '=', $id)
            ->orderBy('year', 'DESC')
            ->groupBy('year')
            ->get();
        return response()->json([
            'data' => $years,
            'code' => 200
        ]);
    }

    /**
     * @year get records by year 
     * @quantity get the quantity of records
     */

    public function most_quantity_used($quantity, $year)
    {
        $inventory = DB::table('inventory_treatment')
            ->select(DB::raw('inventory_id, SUM(units) as quantity'))
            ->whereYear('updated_at', $year)
            ->groupBy('inventory_id')
            ->orderby('quantity', 'desc')
            ->take($quantity)
            ->get();

        foreach ($inventory as $index => $item) {
            $inventory_info[$index] = Inventory::select('id', 'name')->where('id', '=', $item->inventory_id)->get();
            $monthly_usage[$index] = $this->get_inventory_usage_units($item->inventory_id, $year);
        }

        foreach ($inventory as $index => $item) {
            $inventory[$index]->inventory_info = $inventory_info[$index];
            $inventory[$index]->monthly_usage = $monthly_usage[$index];
        }
        return response()->json([
            'data' => $inventory,
            'code' => 200
        ]);
    }

    public function get_inventory_usage_units($id, $year)
    {
        $inventory_units = DB::table('inventory_treatment')
            ->select(DB::raw('inventory_id,SUM(units) as units, Month(updated_at) as month, Year(updated_at) as year'))
            ->where('inventory_id', '=', $id)
            ->whereYear('updated_at', $year)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();

        $months_num = Ultilities::getAllMonths('num');
        $final_data = [];

        for ($i = 0; $i < count($months_num); $i++) {
            $temp_data = new stdClass();
            foreach ($inventory_units as $key => $unit) {
                if ($unit->month == $months_num[$i]) {
                    $temp_data->inventory_id = (int)$id;
                    $temp_data->units = $unit->units;
                    $temp_data->month = $months_num[$i];
                    break;
                }
            }
            $tmp = (array)$temp_data;
            if (empty($tmp)) {
                $temp_data->inventory_id = (int)$id;
                $temp_data->units = 0;
                $temp_data->month = $months_num[$i];
                array_push($final_data, $temp_data);
            } else {
                array_push($final_data, $temp_data);
            }
        }

        return $final_data;
        /*  return response()->json([
            'data' => $final_data,
            'code' => 200
        ]); */
    }




    public function lowest_stocks()
    {
        $inventory = Inventory::select('id', 'name', 'stock')
            ->orderBy('stock', 'asc')
            ->take(10)
            ->get();

        return response()->json([
            'data' => $inventory,
            'code' => 200
        ]);
    }

    public function most_profitabe()
    {
        //TODO:

    }
}
