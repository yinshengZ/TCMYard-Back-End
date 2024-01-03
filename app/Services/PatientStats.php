<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Income;
use DB;



class PatientStatController
{
    public function get_most_patients_gender()
    {
        $gender = Patient::select('gender_id', DB::raw('count(*) as total'))->groupBy('gender_id')->orderBy('total', 'DESC')->with('gender')->get();

        return $gender;
    }

    public function get_most_patients_locale()
    {
        $locale = Patient::select('postcode', DB::raw("SUBSTRING_INDEX(postcode,' ',1) as locale"), DB::raw("count(SUBSTRING_INDEX(postcode,' ',1)) as total"))

            ->groupBy('locale')
            ->orderBy('total', 'DESC')
            ->get();
        return $locale;
    }

    public function get_patient_average_spending()
    {
        $patients = Patient::all()->count();
        $all_incomes = Income::sum('amount');

        return $all_incomes / $patients;
    }
}
