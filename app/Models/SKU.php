<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SKU extends Model
{
    use HasFactory;

    protected $table = 'inventory_skus';
    protected $fillable = ['id', 'name', 'description', 'inventory_id', 'stocking_date', 'expiry_date', 'units', 'out_of_stock',];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
