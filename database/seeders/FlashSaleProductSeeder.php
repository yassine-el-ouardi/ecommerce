<?php

namespace Database\Seeders;

use App\Models\FlashSaleProduct;
use Illuminate\Database\Seeder;

class FlashSaleProductSeeder extends Seeder
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
                'product_id' => 88630119,
                'flash_sale_id' => 1,
                'price' => 78,
                'admin_id' => 1
            ],

            [
                'id' => 2,
                'product_id' => 88630122,
                'flash_sale_id' => 1,
                'price' => 78,
                'admin_id' => 1
            ],

            [
                'id' => 3,
                'product_id' => 88630132,
                'flash_sale_id' => 1,
                'price' => 55,
                'admin_id' => 1
            ],
            [
                'id' => 4,
                'product_id' => 88630143,
                'flash_sale_id' => 1,
                'price' => 65,
                'admin_id' => 1
            ],
            [
                'id' => 5,
                'product_id' => 88630145,
                'flash_sale_id' => 1,
                'price' => 80,
                'admin_id' => 1
            ],
            [
                'id' => 15,
                'product_id' => 88630150,
                'flash_sale_id' => 1,
                'price' => 60,
                'admin_id' => 1
            ],
            [
                'id' => 16,
                'product_id' => 88630151,
                'flash_sale_id' => 1,
                'price' => 78,
                'admin_id' => 1
            ],
            [
                'id' => 17,
                'product_id' => 88630152,
                'flash_sale_id' => 1,
                'price' => 80,
                'admin_id' => 1
            ],
            [
                'id' => 18,
                'product_id' => 88630153,
                'flash_sale_id' => 1,
                'price' => 56,
                'admin_id' => 1
            ],


            [
                'id' => 6,
                'product_id' => 88630155,
                'flash_sale_id' => 1,
                'price' => 60,
                'admin_id' => 1
            ],
            [
                'id' => 7,
                'product_id' => 88630156,
                'flash_sale_id' => 1,
                'price' => 80,
                'admin_id' => 1
            ],
            [
                'id' => 8,
                'product_id' => 88630157,
                'flash_sale_id' => 1,
                'price' => 65,
                'admin_id' => 1
            ],
            [
                'id' => 9,
                'product_id' => 88630158,
                'flash_sale_id' => 1,
                'price' => 54,
                'admin_id' => 1
            ],
            [
                'id' => 14,
                'product_id' => 88630159,
                'flash_sale_id' => 1,
                'price' => 45,
                'admin_id' => 1
            ],
            [
                'id' => 13,
                'product_id' => 88630160,
                'flash_sale_id' => 1,
                'price' => 78,
                'admin_id' => 1
            ],
            [
                'id' => 10,
                'product_id' => 88630161,
                'flash_sale_id' => 1,
                'price' => 45,
                'admin_id' => 1
            ],
            [
                'id' => 11,
                'product_id' => 88630162,
                'flash_sale_id' => 1,
                'price' => 56,
                'admin_id' => 1
            ],
            [
                'id' => 12,
                'product_id' => 88630163,
                'flash_sale_id' => 1,
                'price' => 78,
                'admin_id' => 1
            ],
        ];

        foreach ($items as $i) {
            FlashSaleProduct::create($i);
        }
    }
}
