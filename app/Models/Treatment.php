<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Treatment extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'service_id');
    }

    public function inventories()
    {
        return $this->belongsToMany('App\Models\Inventory')->withPivot('units')->withTimestamps();
    }

    public function incomes()
    {
        return $this->hasMany('App\Models\Income', 'treatment_id');
    }

    public function treatment_details()
    {
        return $this->hasMany('App\Models\TreatmentDetails', 'id');
    }
}
