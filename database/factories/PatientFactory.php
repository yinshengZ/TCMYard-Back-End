<?php

namespace Database\Factories;

use App\Models\Patient;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    protected $model = Patient::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->name,
            'last_name' => $this->faker->name,
            'gender_id' => $this->faker->numberBetween(1, 3),
            'postcode' => $this->faker->postcode,
            'telephone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'marital_status_id' => $this->faker->numberBetween(1, 3),
            'occupation' => $this->faker->jobTitle,
            'hiv_status' => $this->faker->numberBetween(0, 1),
            'past_history' => $this->faker->text($maxNbChars = 200),
            'current_issue' => $this->faker->text($maxNbChars = 200),
            'date_of_birth' => $this->faker->dateTimeThisCentury($max = 'now', $timezone = null),
            'date_joined' => $this->faker->dateTimeThisCentury($max = 'now', $timezone = null)

        ];
    }
}
