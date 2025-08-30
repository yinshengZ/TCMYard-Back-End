<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkuUsage extends Model
{
    use HasFactory;
    protected $table = 'sku_usages';
    protected $fillable = ['sku_id', 'treatment_id', 'description', 'usage_date', 'used_units'];
    public function sku()
    {
        return $this->belongsTo(SKU::class);
    }
}
