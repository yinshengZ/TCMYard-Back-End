<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Income;
use App\Models\Patient;

use App\Services\FinanceService;
use App\Services\Ultilities;


use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use DB;



class FinanceController extends Controller
{

   public function get_income_years()
   {
      $years = Income::select(DB::raw('Year(date) as year'))
         ->groupBy('year')
         ->orderBy('year', 'DESC')
         ->get();
      return response()->json([
         'data' => $years,
         'code' => 200,
      ]);
   }

   public function get_income_by_year($year)
   {
      $incomes = Income::whereYear('date', $year)
         ->with('payment_method')
         ->with('service')
         ->orderBy('date', 'DESC')
         ->get();
      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }

   public function get_income_by_id($id)
   {
      $income = Income::where('id', $id)
         ->with('patient')
         ->with('payment_method')
         ->with('service')
         ->with('user')
         ->get();

      return response()->json([
         'data' => $income,
         'code' => 200
      ]);
   }

   public function add_patient_income(Request $request)
   {
      return $request;
   }

   public function get_curret_year_incomes()
   {
      $incomes = Income::select('amount')->whereYear('date', date('Y'))->sum('amount');
      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }

   public function get_current_month_incomes()
   {
      $incomes = Income::select('amount')->whereYear('date', Carbon::now()->year)->whereMonth('date', Carbon::now()->month)->sum('amount');
      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }

   public function get_current_week_incomes()
   {
      $incomes = Income::select('amount')->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');
      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }

   public function get_current_day_incomes()
   {
      $incomes = Income::select('amount')->whereDate('date', Carbon::today())->sum('amount');
      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }

   public function get_current_day_daily_incomes()
   {
      $incomes = Income::select('amount')
         ->whereDate('date', Carbon::today())
         ->get();
      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }

   public function get_current_day_daily_income_distribution()
   {
      $incomes = Income::select([DB::raw("SUM(amount) as amount"), DB::raw("service_id as service_id"), DB::raw('date as date')])
         ->whereDate('date', Carbon::today())
         ->groupBy('service_id')
         ->orderBy('amount', 'DESC')
         ->with('service')
         ->get();
      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }

   public function get_yearly_incomes()
   {
      /*  $incomes = Income::select(DB::raw("(sum(amount)) as amount"),DB::raw("(DATE_FORMAT(date,'%Y')) as date"))
      ->orderBy('date','DESC')
      ->groupBy(DB::raw("DATE_FORMAT(date,'%Y')"))
      ->get();

      $data = FinanceService::financeDataReconstruct($incomes);



      return $data; */

      //currently not used


   }

   public function get_current_month_daily_incomes()
   {

      $incomes = Income::select([DB::raw("SUM(amount) as amount"), DB::raw('date as date')])
         ->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()])
         ->groupBy('date')
         ->orderBy('date', 'ASC')->get();

      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }

   public function get_current_week_daily_incomes()
   {
      $incomes = Income::select([DB::raw("SUM(amount) as amount"), DB::raw('date as date')])
         ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()])
         ->groupBy('date')
         ->orderBy('date', 'ASC')->get();
      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }


   public function get_current_year_monthly_incomes()
   {
      $incomes = Income::select([DB::raw("SUM(amount) as amount"), DB::raw(" date as date ")])
         ->whereBetween('date', [Carbon::now()->startOfYear(), Carbon::now()])
         ->groupBy(DB::raw('MONTH(date)'))
         ->orderBy('date', 'ASC')->get();
      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }


   public function get_current_year_income_distribution()
   {


      $incomes = Income::select([DB::raw("SUM(amount) as amount"), DB::raw("service_id as service_id"), DB::raw("date as date")])
         ->whereBetween('date', [Carbon::now()->startOfYear(), Carbon::now()])
         ->groupBy('service_id')
         ->orderBy('amount', 'DESC')
         ->with('service')
         ->get();

      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }

   public function get_current_month_income_distribution()
   {
      $incomes = Income::select([DB::raw("SUM(amount) as amount"), DB::raw("service_id as service_id"), DB::raw('date as date')])
         ->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()])
         ->groupBy('service_id')
         ->orderBy('amount', 'DESC')
         ->with('service')
         ->get();

      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }

   public function get_current_week_income_distribution()
   {
      $incomes = Income::select([DB::raw("SUM(amount) as amount"), DB::raw("service_id as service_id"), DB::raw("date as date")])
         ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()])
         ->groupBy('service_id')
         ->orderBy('amount', 'DESC')
         ->with('service')
         ->get();
      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }


   public function get_weekly_comp_incomes()
   {
      $incomes = Income::select([DB::raw("SUM(amount) as amount"), DB::raw("YEAR(date) as year, MONTH(date) as month, WEEK(date) as week")])
         ->groupBy('year', 'month', 'week')
         ->orderBy('date', 'ASC')
         ->whereYear('date', '=', Carbon::now()->year)
         ->get();

      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }


   public function get_monthly_comp_incomes()
   {
      $incomes = Income::select([
         DB::raw("SUM(amount) as amount"), DB::raw("YEAR(date) as year, MONTH(date) as month"),
         DB::raw("date as date")
      ])
         ->groupBy('year', 'month')
         ->orderBy('date', 'DESC')
         ->whereMonth('date', '=', Carbon::now()->month)
         ->get();

      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }


   public function get_yearly_comp_incomes()
   {
      $incomes = Income::select([DB::raw("SUM(amount) as amount"), DB::raw("YEAR(date) as year"), DB::raw("date as date")])
         ->groupBy('year')
         ->orderBy('date', 'DESC')
         ->get();

      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }

   //income distributions data
   public function get_year_income_distributions($data)
   {
      $incomes = Income::select([
         DB::raw("SUM(amount) as amount"), DB::raw("COUNT(amount) as transactions"),
         'service_id', 'date'
      ])
         ->groupBy('service_id')
         ->with('service')
         ->whereYear('date', '=', $data)
         ->get();
      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }

   public function get_week_income_distributions($data)
   {

      $year = strval(Carbon::now()->year);
      $date = Carbon::now();
      $date->setISODate($year, $data);

      $from = $date->copy()->startOfWeek();
      $to = $date->copy()->endOfWeek();

      $income = Income::select([
         DB::raw("SUM(amount) as amount"), DB::raw("COUNT(amount) as transactions"),
         DB::raw("service_id as service_id"), DB::raw("date as date")
      ])
         ->groupBy('service_id')
         ->with('service')
         ->whereBetween('date', [$from, $to])
         ->get();


      return response()->json([
         'data' => $income,
         'code' => 200
      ]);



      /*    return gettype($year);
      
     $date->getDateFromWeek($year, $data); */
   }


   /**
    * get the sum of each services income
    */

   public function get_all_time_comp_percentages()
   {
      $incomes = Income::select([DB::raw("SUM(amount) as amount"), 'service_id'])
         ->groupBy('service_id')
         ->with('service')
         ->get();
      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }
   /**
    * get the number of each services been sold
    */
   public function get_all_time_count_percentages()
   {
      $incomes = Income::select([DB::raw("COUNT(amount) as total_count"), 'service_id'])
         ->groupBy('service_id')
         ->with('service')
         ->get();
      return response()->json([
         'data' => $incomes,
         'code' => 200
      ]);
   }

   public function get_highest_daily_income()
   {
      $income = Income::select([DB::raw("SUM(amount) as amount"), DB::raw("COUNT(amount) as transactions"), DB::raw("DAY(date) as day, YEAR(date) as year, MONTH(date) as month"), DB::raw("date as date")])
         ->groupBy('year', 'month', 'day')
         ->orderBy('amount', 'DESC')
         ->first();

      return response()->json([
         'data' => $income,
         'code' => 200
      ]);
   }

   public function get_highest_weekly_income()
   {
      $income = Income::select([DB::raw("SUM(amount) as amount"), DB::raw("COUNT(amount) as transactions"), DB::raw("WEEK(date) as week, YEAR(date) as year, MONTH(date) as month")])
         ->groupby('year', 'month', 'week')
         ->orderBy('amount', 'DESC')
         ->first();
      return response()->json([
         'data' => $income,
         'code' => 200
      ]);
   }

   public function get_highest_monthly_income()
   {
      $income = Income::select([DB::raw("SUM(amount) as amount"), DB::raw("COUNT(amount) as transactions"), DB::raw("date as date"), DB::raw("MONTH(date) as month, YEAR(date) as year")])
         ->groupBy('year', 'month')
         ->orderBy('amount', 'DESC')
         ->first();

      return response()->json([
         'data' => $income,
         'code' => 200
      ]);
   }

   public function get_highest_yearly_income()
   {
      $income = Income::select([DB::raw("SUM(amount) as amount"), DB::raw("COUNT(amount) as transactions"), DB::raw("date as date"), DB::raw("YEAR(date) as year")])
         ->groupBy('year')
         ->orderBy('amount', 'DESC')
         ->first();
      return response()->json([
         'data' => $income,
         'code' => 200
      ]);
   }

   public function get_highest_spender()
   {
      $income = Income::select([
         DB::raw("SUM(amount) as amount"),
         DB::raw("patient_id as patient_id"), DB::raw("COUNT(amount) as transactions")
      ])
         ->groupBy('patient_id')
         ->orderBy('amount', 'DESC')
         ->with('patient')
         ->first();
      return response()->json([
         'data' => $income,
         'code' => 200
      ]);
   }







   public function get_all_incomes()
   {
   }

   public function get_patient_incomes($id)
   {
      /* $income = Income::where('patient_id',$id)->with('payment_method','service')->get();
      return $income; */
      $income = FinanceService::processIncomesData($id);
      return response()->json([
         'data' => $income,
         'code' => 200
      ]);
   }

   public function update_patient_income(Request $request)
   {
      return $request;
   }

   public function delete_patient_income($id)
   {
      return $id;
   }

   public function add_expense(Request $request)
   {
      return $request;
   }

   public function get_all_expenses()
   {
      return null;
   }

   public function get_user_expenses($id)
   {
      return $id;
   }

   public function update_expense(Request $request)
   {
      return $request;
   }

   public function delete_expense($id)
   {
      return $id;
   }
}
