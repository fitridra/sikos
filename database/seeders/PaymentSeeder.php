<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    public function run()
    {
        Payment::insert([
            [
                'member_id'    => 1,
                'payment_date' => Carbon::now()->subDays(20)->toDateString(),
                'amount'       => 1000000,
            ],
            [
                'member_id'    => 2,
                'payment_date' => Carbon::now()->subDays(10)->toDateString(),
                'amount'       => 1000000,
            ],
            [
                'member_id'    => 3,
                'payment_date' => Carbon::now()->subDays(5)->toDateString(),
                'amount'       => 1200000,
            ],
        ]);
    }
}
