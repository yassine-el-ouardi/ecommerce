<?php

namespace Database\Seeders;

use App\Models\Withdrawal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class WithdrawalSeeder extends Seeder
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
                'amount' => '10',
                'withdrawal_account_id' => 1,
                'status' => Config::get('constants.status.PRIVATE'),
                'admin_id' => 2,
                'approved_by' => 1
            ],
            [
                'amount' => '10',
                'withdrawal_account_id' => 1,
                'status' => Config::get('constants.status.PUBLIC'),
                'admin_id' => 2,
                'approved_by' => 1
            ]
        ];

        foreach ($items as $i) {
            Withdrawal::create($i);
        }
    }
}
