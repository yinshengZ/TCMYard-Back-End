<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
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
            'username' => 'dongsmbm',
            'password' => Hash::make('123456'),
            'name' => 'DongsMBM',            
            'email' => 'xxx@xxx.com',
            'role'=>'admin',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
        User::create($data);

        User::factory(10)->create();
    }
}
