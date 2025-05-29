<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    public function run()
    {
        DB::table('tb_rooms')->insert([
            [
                'kost_id' => 1,
                'room_number' => 'A101',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kost_id' => 1,
                'room_number' => 'A102',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kost_id' => 2,
                'room_number' => 'B201',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kost_id' => 2,
                'room_number' => 'B202',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kost_id' => 3,
                'room_number' => 'C301',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
