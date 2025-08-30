<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory;
    //use SoftDeletes;
    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'categories_id');
    }

    public function treatments()
    {
        return $this->belongsToMany('App\Models\Treatment')->withTimestamps();
    }

    public function stockings()
    {
        return $this->hasMany('App\Models\InventoryStocking', 'inventory_id');
    }

    public function skus()
    {
        return $this->hasMany('App\Models\SKU', 'inventory_id');
    }



    /*  public function treatment_details()
    {
        return $this->BelongsToMany('App\models\TreatmentDetails')->withTimestamps();
    } */
}
