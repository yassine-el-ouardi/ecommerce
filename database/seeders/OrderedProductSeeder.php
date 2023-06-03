<?php

namespace Database\Seeders;

use App\Models\OrderedProduct;
use Illuminate\Database\Seeder;

class OrderedProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'id' => 1,
                'product_id' => 88630158,
                'inventory_id' => 48,
                'quantity' => 1,
                'shipping_place_id' => 1,
                'shipping_type' => 1,
                'purchased' => 90,
                'selling' => 110,
                'tax_price' => 1.3,
                'shipping_price' => 10,
                'order_id' => 1
            ],
            [
                'id' => 2,
                'product_id' => 88630162,
                'inventory_id' => 52,
                'quantity' => 1,
                'shipping_place_id' => 1,
                'shipping_type' => 1,
                'purchased' => 90,
                'selling' => 100,
                'tax_price' => 1.3,
                'shipping_price' => 10,
                'commission' => 20,
                'commission_amount' => 20,
                'order_id' => 1
            ],
            [
                'id' => 3,
                'product_id' => 88630163,
                'inventory_id' => 53,
                'quantity' => 1,
                'shipping_place_id' => 1,
                'shipping_type' => 1,
                'purchased' => 90,
                'selling' => 100,
                'tax_price' => 1.3,
                'shipping_price' => 10,
                'commission' => 20,
                'commission_amount' => 20,
                'order_id' => 2
            ]
        ];

        foreach ($items as $i) {
            OrderedProduct::create($i);
        }
    }
}
