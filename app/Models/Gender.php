<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gender extends Model
{
    use HasFactory;
    use SoftDeletes;
    public function patients(){
        return $this->hasMany('App\Models\Patient','gender_id');
    }
}
