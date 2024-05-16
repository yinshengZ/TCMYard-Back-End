<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Expense;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model=Expense::class;
    public function definition(): array
    {
        return [
            'amount'=>$this->faker->randomFloat(2),
            'description'=>$this->faker->sentence(),
            'date'=>$this->faker->date('Y-m-d'),
            'patient_id'=>$this->faker->numberBetween(1,11),
            'expense_category_id'=>$this->faker->numberBetween(1,2),
            'payment_method_id'=>$this->faker->numberBetween(1,2)
            

        ];
    }
}
