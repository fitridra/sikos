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
                'payment_date' => '2025-01-10',
                'duration'     => 'monthly',
                'amount'       => 1000000,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'member_id'    => 2,
                'payment_date' => '2024-11-13',
                'duration'     => '6months',
                'amount'       => 6000000,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'member_id'    => 3,
                'payment_date' => '2025-03-20',
                'duration'     => 'yearly',
                'amount'       => 14000000,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}
