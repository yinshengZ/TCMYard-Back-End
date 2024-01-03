<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomeType extends Model
{
    protected $table="income_type";
    use HasFactory;
    use SoftDeletes;

    public function incomes(){
        return $this->hasMany('App\Models\Income','income_type_id');
    }
}
