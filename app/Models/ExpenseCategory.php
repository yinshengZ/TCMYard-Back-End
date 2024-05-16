<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $table = 'expense_categories';
    use HasFactory;

    public function expense(){
        return $this->hasMany('App\Models\Expense','expense_category_id');
    }
    
}
