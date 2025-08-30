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
        return $this->belongsToMany('App\Models\Inventory')->withTimestamps();
    }

    /*  public function inventories_pivot(){
        return $this->hasMany('App\Models\Inventory','')
    } */


    public function incomes()
    {
        return $this->hasMany('App\Models\Income', 'id');
    }

    public function treatment_details()
    {
        return $this->hasMany('App\Models\TreatmentDetails', 'id');
    }

    public function patients()
    {
        return $this->belongsTo('App\Models\Patient', 'patient_id');
    }
}
