<?php

namespace Database\Factories;

use App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model=Inventory::class;
    public function definition()
    {
        return [
            'name'=>$this->faker->name,
            'eng_name'=>$this->faker->name,
            'description'=>$this->faker->realText($maxNbChars=200, $indexSize=2),
            'stock'=>$this->faker->randomNumber($nbDigits=null),
            'categories_id'=>$this->faker->numberBetween($min=1,$max=4),
            'unit_price'=>$this->faker->randomFloat($nbMaxDecimals = 2, $min=1,$max=null),
            'expiry_date'=>$this->faker->dateTimeBetween($startDate = 'now', $endDate='+5years')

        ];
    }
}
