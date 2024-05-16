<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;
    protected $table='payment_method';

    public function income(){
        return $this->hasMany('App\Models\Income','payment_type_id');
    }

    public function expense(){
        return $this->hasMany('App\Models\Expense','payment_type_id');
    }
}
