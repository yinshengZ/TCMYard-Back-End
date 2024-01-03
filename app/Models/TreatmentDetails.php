<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentDetails extends Model
{
    use HasFactory;

    public function treatment()
    {
        return $this->belongsTo('App\Models\Treatment', 'treatment_id', 'id');
    }

    /*    public function inventory_details()
    {
        return $this->hasMany('App\Models\Inventory', 'id', 'inventory_id');
    } */

    public function inventory()
    {
        return $this->belongsToMany('App\Models\Inventory')->withTimestamps();
    }
}
