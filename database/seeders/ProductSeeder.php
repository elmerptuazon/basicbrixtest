<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                'name' => 'Almond',
                'available_quantity' => 500,
                'price' => 5.00,
            ],
            [
                'name' => 'Butter',
                'available_quantity' => 100,
                'price' => 3.00,
            ],
        ];

        foreach($products as $product) {
            DB::table('products')->insert($product);
        }
        
    }
}
