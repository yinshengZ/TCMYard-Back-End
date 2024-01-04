<?php

namespace App\Http\Controllers;

use DB;

use Illuminate\Http\Request;
use App\Models\Inventory;

use illuminate\Support\Carbon;


class InventoryStatController extends Controller
{
    public function get_expirying_inventories()
    {
        $expirying_inventory = Inventory::where('expiry_date', '<', Carbon::now()->addDays(50))
            ->orderBy('expiry_date', 'ASC')
            ->get();
        return $expirying_inventory;
    }
}
