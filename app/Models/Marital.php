<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Marital extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table='marital_status';

    public function patients(){
        return $this->hasMany('App\Models\Patient','marital_status_id');
    }
}
