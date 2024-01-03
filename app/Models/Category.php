<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    protected $table="inventory_categories";
    use HasFactory;
    use SoftDeletes;

    public function inventories(){
        return $this->hasMany('App\Models\Inventory','categories_id');
    }

    public function treatments(){
        return $this->hasMany('App\Models\Treatment','service_id');
    }
    
   

    public function incomes(){
        return $this->hasMany('App\Models\Income','service_id');
    }
    
}
