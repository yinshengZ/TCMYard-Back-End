<?php

namespace Database\Seeders;

use App\Models\Patient;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('zh_CN');
        $data = [
            'first_name' => 'yinsheng',
            'last_name' => 'zhou',
            'gender_id' => 1,
            'postcode' => 'CR0 4NB',
            'telephone' => '07908153858',
            'email' => 'dongsmbm@gmail.com',
            'marital_status_id' => '1',
            'occupation' => 'employed',
            'hiv_status' => 0,
            'past_history' => 'askjdhkashdjkhasdkhjaskd',
            'current_issue' => 'aksjdhkjashdhaskjdhjahsd',
            'date_of_birth' => Carbon::create('1991','04','20'),
            'date_joined' => Carbon::create('1991','04','20')
        ];
        Patient::create($data);
        Patient::factory(500)->create();
    }
}
