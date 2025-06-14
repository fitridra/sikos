<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tb_members')->insert([
            [
                'full_name'     => 'Mawar Lestari',
                'address'       => 'DKI Jakarta',
                'phone'         => '081234567890',
                'room_id'       => 2,
                'move_in_date'  => Carbon::parse('2025-01-10'),
                'move_out_date' => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'full_name'     => 'Rina Kartika',
                'address'       => 'Bekasi',
                'phone'         => '081324567890',
                'room_id'       => 1,
                'move_in_date'  => Carbon::parse('2024-11-13'),
                'move_out_date' => Carbon::parse('2025-05-15'),
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'full_name'     => 'Budi Santoso',
                'address'       => 'Tangerang',
                'phone'         => '081543267890',
                'room_id'       => 5,
                'move_in_date'  => Carbon::parse('2025-03-20'),
                'move_out_date' => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}
