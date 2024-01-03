<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Marital;

class MaritalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=[
        [
          'marital_status'=>'single',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()  
        ],
        [
            'marital_status'=>'married',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ],
        [
            'marital_status'=>'divorced',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]
            
        ];
        
        Marital::insert($data);
    }
}
