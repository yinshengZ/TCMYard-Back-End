<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Income extends Model
{
    use HasFactory;

    public function patient()
    {
        return $this->belongsTo('App\Models\Patient', 'patient_id');
    }

    public function treatment()
    {
        return $this->belongsTo('App\Models\Treatment', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    public function payment_method()
    {
        return $this->belongsTo('App\Models\PaymentMethod', 'payment_type_id');
    }

    public function service()
    {
        return $this->belongsTo('App\Models\Category', 'service_id');
    }
}
