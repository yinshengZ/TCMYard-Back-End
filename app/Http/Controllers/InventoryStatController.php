<?php

namespace App\Http\Controllers;

use DB;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Treatment;

use illuminate\Support\Carbon;


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

    public function most_used_inventories()
    {

        $inventory = DB::table('inventory_treatment')
            ->select(DB::raw('inventory_id, COUNT(inventory_id) as counts'))
            ->groupBy('inventory_id')
            ->orderBy('counts', 'desc')
            ->take(10)
            ->get();

        foreach ($inventory as $index => $item) {
            $inventory_info[$index] = Inventory::select('id', 'name')->where('id', '=', $item->inventory_id)->get();
            /*             $inventory[$index]->put('inventory_info', $inventory_info);
 */
        }

        foreach ($inventory as $index => $item) {
            $inventory[$index]->inventory_info = $inventory_info[$index];
        }



        return response()->json([
            'data' => $inventory,
            'code' => 200
        ]);
    }
}
