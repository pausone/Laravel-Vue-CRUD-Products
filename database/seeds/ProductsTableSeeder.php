<?php

use Illuminate\Database\Seeder;
use App\Product;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::truncate();

        $faker = \Faker\Factory::create();
        $imagenumber = 1;

        for ($i=0; $i < 20; $i++) {
            Product::create([
                'name' => ucfirst($faker->words($nb = 2, $asText = true)),
                'description' => $faker->paragraph,
                'price' => $faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 500), // 48.8932
                'image' => "http://lorempixel.com/150/150/technics/$imagenumber"//$faker->imageUrl(150, 150, 'technics')
            ]);

            if($imagenumber < 10)
                $imagenumber++;
            else
                $imagenumber = 1;
        }


    }
}
