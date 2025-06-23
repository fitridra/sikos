<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    public function run()
    {
        $today = Carbon::today();

        Payment::insert([
            [
                'member_id'    => 1,
                'payment_date' => $today->copy()->subMonth(),
                'duration'     => 'monthly',
                'amount'       => 1000000,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'member_id'    => 2,
                'payment_date' => $today->copy()->subMonths(6),
                'duration'     => '6months',
                'amount'       => 6000000,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'member_id'    => 3,
                'payment_date' => $today->copy()->subYear(),
                'duration'     => 'yearly',
                'amount'       => 14000000,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}
