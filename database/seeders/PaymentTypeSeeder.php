<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\PaymentMethod;

use Carbon\Carbon;

class PaymentTypeSeeder extends Seeder
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
                'payment_type'=>'card',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'payment_type'=>'cash',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
          
        ];
        PaymentMethod::insert($data);
    }
}
