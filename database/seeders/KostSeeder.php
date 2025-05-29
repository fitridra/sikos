<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KostSeeder extends Seeder
{
    public function run()
    {
        DB::table('tb_kosts')->insert([
            [
                'kost_name' => 'Kost Merdeka',
                'address' => 'Jl. Merdeka No.10, Jakarta',
                'amount' => 1000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kost_name' => 'Kost Harmoni',
                'address' => 'Jl. Harmoni No.25, Bandung',
                'amount' => 800000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kost_name' => 'Kost Sejahtera',
                'address' => 'Jl. Sejahtera No.45, Surabaya',
                'amount' => 1200000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
