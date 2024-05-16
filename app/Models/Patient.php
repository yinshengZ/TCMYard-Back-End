<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Disease;

class Patient extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function gender()
    {
        return $this->belongsTo('App\Models\Gender', 'gender_id');
    }

    public function marital_status()
    {
        return $this->belongsTo('App\Models\Marital', 'marital_status_id');
    }

    public function allergies()
    {
        return $this->belongsToMany('App\Models\Allergy')->withTimestamps();
    }

    public function medications()
    {
        return $this->belongsToMany('App\Models\Medication')->withTimestamps();
    }

    public function symptoms()
    {
        return $this->belongsToMany('App\Models\Symptom')->withTimestamps();
    }

    public function diseases()
    {
        return $this->belongsToMany(Disease::class, 'disease_patient')->withTimestamps();
    }

    public function records()
    {
        return $this->hasMany('App\Models\Record', 'patient_id');
    }

    public function incomes()
    {
        return $this->hasMany('App\Models\Income', 'patient_id');
    }

    public function files()
    {
        return $this->hasmany('App\Models\File', 'patient_id');
    }

    public function treatments()
    {
        return $this->hasMany('App\Models\Treatment', 'patient_id');
    }

    public function expenses(){
        return $this->hasMany('App\Models\Expense','patient_id');
    }
}
