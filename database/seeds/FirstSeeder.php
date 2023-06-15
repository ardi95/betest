<?php

use Illuminate\Database\Seeder;
use App\Balance;
use App\Product;

class FirstSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Balance::create();

        Product::create([
            'name' => 'Biskuit',
            'price' => 6000,
            'qty' => 10
        ]);

        Product::create([
            'name' => 'Chips',
            'price' => 8000,
            'qty' => 10
        ]);

        Product::create([
            'name' => 'Oreo',
            'price' => 10000,
            'qty' => 10
        ]);

        Product::create([
            'name' => 'Tango',
            'price' => 12000,
            'qty' => 10
        ]);

        Product::create([
            'name' => 'Cokelat',
            'price' => 15000,
            'qty' => 10
        ]);
    }
}
