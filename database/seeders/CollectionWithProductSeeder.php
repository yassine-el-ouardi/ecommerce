<?php

namespace Database\Seeders;

use App\Models\CollectionWithProduct;
use Illuminate\Database\Seeder;

class CollectionWithProductSeeder extends Seeder
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
                'product_collection_id' => 1,
                'product_id' => 88630111
            ],
            [
                'product_collection_id' => 2,
                'product_id' => 88630112
            ],
            [
                'product_collection_id' => 3,
                'product_id' => 88630113
            ],
            [
                'product_collection_id' => 1,
                'product_id' => 88630114
            ],
            [
                'product_collection_id' => 2,
                'product_id' => 88630115
            ],
            [
                'product_collection_id' => 3,
                'product_id' => 88630116
            ],
            [
                'product_collection_id' => 1,
                'product_id' => 88630117
            ],
            [
                'product_collection_id' => 3,
                'product_id' => 88630119
            ],
            [
                'product_collection_id' => 1,
                'product_id' => 88630120
            ],
            [
                'product_collection_id' => 2,
                'product_id' => 88630121
            ],
            [
                'product_collection_id' => 3,
                'product_id' => 88630122
            ],
            [
                'product_collection_id' => 1,
                'product_id' => 88630123
            ],
            [
                'product_collection_id' => 2,
                'product_id' => 88630124
            ],
            [
                'product_collection_id' => 3,
                'product_id' => 88630125
            ],
            [
                'product_collection_id' => 1,
                'product_id' => 88630126
            ],
            [
                'product_collection_id' => 2,
                'product_id' => 88630127
            ],
            [
                'product_collection_id' => 3,
                'product_id' => 88630128
            ],
            [
                'product_collection_id' => 1,
                'product_id' => 88630129
            ],
            [
                'product_collection_id' => 2,
                'product_id' => 88630130
            ],
            [
                'product_collection_id' => 3,
                'product_id' => 88630131
            ],
            [
                'product_collection_id' => 1,
                'product_id' => 88630132
            ],
            [
                'product_collection_id' => 2,
                'product_id' => 88630133
            ],
            [
                'product_collection_id' => 3,
                'product_id' => 88630134
            ],
            [
                'product_collection_id' => 1,
                'product_id' => 88630135
            ],
            [
                'product_collection_id' => 2,
                'product_id' => 88630136
            ],
            [
                'product_collection_id' => 3,
                'product_id' => 88630137
            ]
        ];

        foreach ($items as $i) {
            CollectionWithProduct::create($i);
        }
    }
}
