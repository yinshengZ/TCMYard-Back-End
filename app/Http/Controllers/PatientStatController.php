<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Income;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

use App\Services\Ultilities;
use stdClass;

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

    public function get_newest_patients()
    {
        $patient = Patient::orderBy('date_joined', 'desc')->take(10)->get();

        return response()->json([
            'data' => $patient,
            'code' => 200
        ]);
    }

    public function get_most_profitable_patients()
    {
        $patient = Income::select('amount', 'patient_id', DB::raw('SUM(amount) as patient_spending'))
            ->groupBy('patient_id')
            ->orderBy('patient_spending', 'desc')
            ->take(10)
            ->with('patient')
            ->get();
        return response()->json([
            'data' => $patient,
            'code' => 200
        ]);
    }


    public function get_current_year_monthly_patients()
    {    

        $patients = Patient::select([DB::raw("COUNT(id) as count"), DB::raw("MONTH(date_joined) as date"), 'first_name'])
            ->whereBetween('date_joined', [Carbon::now()->startOfYear(), Carbon::now()])
            ->groupBy(DB::raw('date'))
            ->orderBy('date', 'ASC')->get();

        if (count($patients) > 0) {
            $months = Ultilities::getAllMonths('short');
            $num_months = Ultilities::getAllMonths('num');
            $count = 0;
            $date = 0;
            $first_name = "Null";
            $final_data = [];
            $patients_data=$patients->shift();

          for ($i = 0; $i<count($num_months); $i++){              
              
              
            if($num_months[$i] == $patients_data->date){
                    $patients_data->short_month = $months[$i];
                    array_push($final_data, $patients_data);
                    if($patients->count()>0){
                        $patients_data = $patients->shift();
                      }
                    continue;
                }  else{
                    $date = (int)$num_months[$i];
                    $month_data = new stdClass();
                    $month_data->count=$count;
                    $month_data->date=$date;
                    $month_data->short_month = $months[$i];
                    $month_data->first_name=$first_name;            
                    array_push($final_data,$month_data);         
    
                   }
                   
              }              
             
            }
            return response()->json([
                'data'=>$final_data,
                'code'=>200
            ]);

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
