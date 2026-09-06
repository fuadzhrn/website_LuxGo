<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Structure and shared settings only. No user account is seeded — admin
     * accounts are created deliberately, never with a default password.
     */
    public function run(): void
    {
        $this->call([
            PagesSeeder::class,
            MembershipSettingsSeeder::class,
            SiteSettingsSeeder::class,
            VehicleSeeder::class,
        ]);
    }
}
