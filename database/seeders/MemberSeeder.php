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
        $today = Carbon::today();

        DB::table('tb_members')->insert([
            [
                'full_name'     => 'Mawar Lestari',
                'address'       => 'DKI Jakarta',
                'phone'         => '081234567890',
                'room_id'       => 2,
                'move_in_date'  => $today->copy()->subMonth(), // 1 bulan sebelumnya
                'move_out_date' => $today,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'full_name'     => 'Rina Kartika',
                'address'       => 'Bekasi',
                'phone'         => '081324567890',
                'room_id'       => 1,
                'move_in_date'  => $today->copy()->subMonths(6), // 6 bulan sebelumnya
                'move_out_date' => $today,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'full_name'     => 'Budi Santoso',
                'address'       => 'Tangerang',
                'phone'         => '081543267890',
                'room_id'       => 5,
                'move_in_date'  => $today->copy()->subYear(), // 1 tahun sebelumnya
                'move_out_date' => $today,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}
