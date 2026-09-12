<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Must run first: SwanContentSeeder assigns the seeded admin the
        // super_admin role, which doesn't exist until this creates it.
        $this->call(ShieldSeeder::class);
        $this->call(SwanContentSeeder::class);
        $this->call(DemoContentSeeder::class);
    }
}
