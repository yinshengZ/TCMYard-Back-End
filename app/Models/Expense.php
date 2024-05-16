<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $table="expenses";

    public function category(){
        return $this->belongsTo('App\Models\ExpenseCategory','expense_category_id');
    }

    public function patient(){
        return $this->belongsTo('App\Models\Patient','patient_id');
    }
    public function payment_method(){
        return $this->belongsTO('App\Models\PaymentMethod','payment_method_id');
    }
}
