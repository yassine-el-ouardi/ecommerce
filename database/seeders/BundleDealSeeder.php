<?php

namespace Database\Seeders;

use App\Models\BundleDeal;
use Illuminate\Database\Seeder;

class BundleDealSeeder extends Seeder
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
                'title' => 'BOGO',
                'buy' => 2,
                'free' => 1,
                'admin_id' => 1
            ]
        ];

        foreach ($items as $i) {
            BundleDeal::create($i);
        }
    }
}
