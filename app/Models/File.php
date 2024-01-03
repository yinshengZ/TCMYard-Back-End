<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table="files";
    use HasFactory;

    public function patient(){
        return $this->belongsTo('App\Models\Patient','patient_id');
    }

    public function user(){
        return $this->belongsTo('App\Models\User','user_id');
    }
}
