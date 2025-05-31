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
                'payment_date' => $date = Carbon::now()->subDays(20)->toDateString(),
                'payment_month' => Carbon::parse($date)->month,
                'payment_year'  => Carbon::parse($date)->year,
                'amount'       => 1000000,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'member_id'    => 2,
                'payment_date' => $date = Carbon::now()->subDays(10)->toDateString(),
                'payment_month' => Carbon::parse($date)->month,
                'payment_year'  => Carbon::parse($date)->year,
                'amount'       => 1000000,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'member_id'    => 3,
                'payment_date' => $date = Carbon::now()->subDays(5)->toDateString(),
                'payment_month' => Carbon::parse($date)->month,
                'payment_year'  => Carbon::parse($date)->year,
                'amount'       => 1200000,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}
