<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            GenderSeeder::class,
            PatientSeeder::class,
            MaritalSeeder::class,
            Inventory_Categories_Seeder::class,
            PaymentTypeSeeder::class,
            InventorySeeder::class,
            //IncomeSeeder::class,
            //ExpenseSeeder::class,

        ]);
    }
}
