<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpendingSeeder extends Seeder
{
    public function run()
    {
        DB::table('tb_spending')->insert([
            [
                'kost_id' => 1,
                'spending_name' => 'Listrik',
                'spending_date' => now(),
                'amount' => 350000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kost_id' => 2,
                'spending_name' => 'Air',
                'spending_date' => now(),
                'amount' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
