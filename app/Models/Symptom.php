<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Symptom extends Model
{
    use HasFactory;
    use SoftDeletes;
    public function patients(){
        return $this->belongsToMany('App\Models\Patient')->withTimestamps();
    }
}
