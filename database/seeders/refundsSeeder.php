<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class RefundsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 2000) as $index) {
            $applicationDate = $faker->dateTimeBetween('2019-06-01', '2024-05-31')->format('Y-m-d');
            $deliveryDate = Carbon::createFromFormat('Y-m-d', $applicationDate)->addDays($faker->randomElement([3, 5]))->format('Y-m-d');

            DB::table('refunds')->insert([
                'date' => $deliveryDate,
                'reason' => $faker->text(), // Usar $faker->text() para generar texto aleatorio
                'quantity' => $faker->numberBetween(1, 5),
                'customer_id' => $faker->numberBetween(1, 4000),
                'detail_sale_id' => $faker->numberBetween(1, 21905),
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ]);
        }
    }
}
