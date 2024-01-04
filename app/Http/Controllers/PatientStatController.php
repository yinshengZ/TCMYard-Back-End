<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Income;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

use App\Services\Ultilities;

class PatientStatController extends Controller
{
    public function get_custom_number_patients($request)
    {
        $patient = Patient::with('gender')->orderBy('created_at', 'DESC')->paginate($request);
        return response()->json([
            'data' => $patient,
            'code' => 200
        ]);
    }



    public function get_current_year_monthly_patients()
    {
        $data = [];
        $counter = 0;

        $patients = Patient::select([DB::raw("COUNT(id) as count"), DB::raw("MONTH(date_joined) as date"), 'first_name'])
            ->whereBetween('date_joined', [Carbon::now()->startOfYear(), Carbon::now()])
            ->groupBy(DB::raw('date'))
            ->orderBy('date', 'ASC')->get();
        if ($patients->count() > 0) {
            $months = Ultilities::getAllMonths('short');
            $current_month = Carbon::now()->month;
            //index start from 1 as its used to compare month value of 1, <= used to loop 12 times for 12 months
            for ($i = 1; $i <= (int)$current_month; $i++) {

                if ($patients[$counter]['date'] != $i) {
                    //$i - 1 to get the actual index for data arrays
                    $data[$i - 1]['count'] = 0;
                    $data[$i - 1]['date'] = $months[$i - 1];
                } else {

                    $data[$i - 1]['count'] = $patients[$counter]['count'];
                    $data[$i - 1]['date'] = $months[$i - 1];

                    // increment to maximum the size of the $patients data.
                    if ($counter <= sizeof($patients)) {
                        $counter++;
                    }
                }
            }


            return response()->json([
                'data' => $data,
                'code' => 200
            ]);
        } else {
            return response()->json([
                'data' => "No new patients yet!",
                'code' => 200
            ]);
        }
    }

    public function get_most_patients_gender()
    {
        $gender = Patient::select('gender_id', DB::raw('count(*) as total'))->groupBy('gender_id')->orderBy('total', 'DESC')->with('gender')->get();

        return response()->json([
            'data' => $gender,
            'code' => 200
        ]);
    }

    public function get_most_patients_locale()
    {
        $locale = Patient::select('postcode', DB::raw("SUBSTRING_INDEX(postcode,' ',1) as locale"), DB::raw("count(SUBSTRING_INDEX(postcode,' ',1)) as total"))

            ->groupBy('locale', 'postcode')
            ->orderBy('total', 'DESC')
            ->get();
        return response()->json([
            'data' => $locale,
            'code' => 200
        ]);
    }

    public function get_patient_average_spending()
    {
        $patients = Patient::all()->count();
        $all_incomes = Income::sum('amount');

        return response()->json([
            'data' => $all_incomes / $patients,
            'code' => 200
        ]);
    }
}
