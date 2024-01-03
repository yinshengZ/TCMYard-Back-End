<?php
namespace App\Services;

use App\Models\Category;

use App\Models\Inventory;
use Illuminate\Support\Carbon;
use DB;

use App\Models\Income;


    class FinanceService {


        /**
         * Retrieve all incomes of the patient
         * @param  patient $id
         *  @return collection of incomes $final_data;
         */
        public static function processIncomesData($id){
            $finances = Income::where('patient_id',$id)->with('payment_method')->orderBy('created_at','DESC')->get();
            $final_data = [];
            foreach($finances as $index=>$finance){
                //$service = Inventory::select('categories_id')->where('id',$finance->service_id)->first();
           
                $service_category = Category::where('id',$finance->service_id)->first();
           

                $finance->service = $service_category;

                array_push($final_data,$finance);
            }
        

            return $final_data;

     

        }

        public function reportNill(){
            $income = Income::where('date','=',Carbon::today())->get();
            
            if($income->count()>0){
               return 'there were incomes today!';
            }else{
               $nill_income = new Income;
               $nill_income->amount = 0;
               $nill_income->original_amount= 0;
               $nill_income->payment_type_id= 1;
               $nill_income->patient_id = 0;
               $nill_income->user_id = 0;
               $nill_income->treatment_id = 0;
               $nill_income->service_id = 0;
               $nill_income->discount = 0;
               $nill_income->date= Carbon::today();
               $nill_income->description = "There are no incomes for this day";
      
               $nill_income->save();
      
               return "Nill income has been added!";
      
      
            }
      
          
         }

     


    }



?>