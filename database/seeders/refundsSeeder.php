<?php

namespace Database\Seeders;

use App\Models\refunds;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use function Laravel\Prompts\text;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class refundsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $faker = Faker::create();

        foreach (range(1, 21) as $index) {
            $applicationDate = $faker->dateTimeBetween('2019-06-01', '2024-05-31')->format('Y-m-d');
            $deliveryDate = Carbon::createFromFormat('Y-m-d', $applicationDate)->addDays($faker->randomElement([3, 5]))->format('Y-m-d');

            DB::table('refunds')->insert([
                'date' => $deliveryDate,
                'reason' => $faker->text(), // Usar $faker->text() para generar texto aleatorio
                'quantity' => $faker->numberBetween(1, 5),
                'customer_id' => $faker->numberBetween(1, 2000),
                'detail_sale_id' => $faker->numberBetween(1, 16434),
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ]);
        }
    }
}
