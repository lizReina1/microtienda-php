<?php

namespace Database\Seeders;

use App\Models\detailSale;
use App\Models\sales;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class salesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

            sales::factory(2000)->create()->each(function ($sale) use ($faker) {
            $applicationDate = $faker->dateTimeBetween('2019-06-01', '2024-05-31')->format('Y-m-d');
            $deliveryDate = Carbon::createFromFormat('Y-m-d', $applicationDate)->addDays($faker->randomElement([3, 5]))->format('Y-m-d');

            $sale->update([
                'date' => $deliveryDate,
                //'total' =>  $faker->randomFloat(2, 1, 500),
                'payment_type' => $faker->randomElement(['card', 'qr', 'cash']),
                //'quantity_items' => $faker->numberBetween(1, 20),
                'customer_id' => $faker->numberBetween(1, 2000),
                'user_id' =>  $faker->numberBetween(1, 14),
            ]);

            $numberOfDetails = rand(1, 10);

            for ($i = 0; $i < $numberOfDetails; $i++) {
                $price = $faker->randomFloat(2, 1, 100);
                $quantity = $faker->numberBetween(1, 20);
                detailSale::factory()->create([
                    'price' => $price,
                    'quantity' => $quantity,
                    'total' => $price * $quantity,
                    'sale_id' => $sale->id,
                    'product_id' => $faker->numberBetween(1, 200),
                ]);
            }

            $sale->update([
                'total' => $sale->detailSales->sum('total'),
                'quantity_items' => $sale->detailSales->sum('quantity'),
            ]);
        });
    }
}
