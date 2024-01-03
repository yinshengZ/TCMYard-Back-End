<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $table='medications';

    public function patients(){
        return $this->belongsToMany('App\Models\Patient','medication_id');
    }
}
