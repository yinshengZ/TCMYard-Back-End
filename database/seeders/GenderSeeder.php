<?php

namespace Database\Seeders;

use App\Models\Gender;

use Carbon\Carbon;

use Illuminate\Database\Seeder;


class GenderSeeder extends Seeder
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
            'gender'=>'male',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ],
        [
            'gender'=>'female',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
            
        ],
        [
            'gender'=>'other',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]
        ];
        Gender::insert($data);
    }
}
