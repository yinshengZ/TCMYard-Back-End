<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Record extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function patient(){
        return $this->belongsTo('App\Models\Patient','patient_id');

    }

    public function user(){
        return $this->belongsTo('App\Models\User','user_id');
    }
}

