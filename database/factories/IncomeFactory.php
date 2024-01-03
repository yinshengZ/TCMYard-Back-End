<?php

namespace Database\Factories;

use App\Models\Income;

use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */


     protected $model=Income::class;

    public function definition()
    {
        return [
            'amount'=>$this->faker->randomFloat(2),
            'original_amount'=>$this->faker->randomFloat(2),
            'payment_type_id'=>$this->faker->numberBetween(1,2),
            'patient_id'=>$this->faker->numberBetween(1,11),
            'user_id'=>'1',
            'treatment_id'=>$this->faker->randomNumber(5,false),
            'service_id'=>$this->faker->numberBetween(1,4),
            'discount'=>$this->faker->numberBetween(0,100),
            'date'=>$this->faker->date('Y-m-d'),
            'description'=>$this->faker->sentence(),
        ];
    }
}
