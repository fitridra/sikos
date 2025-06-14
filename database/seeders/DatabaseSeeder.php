<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(KostSeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(MemberSeeder::class);
        $this->call(PaymentSeeder::class);
        $this->call(UserSeeder::class);
    }
}
