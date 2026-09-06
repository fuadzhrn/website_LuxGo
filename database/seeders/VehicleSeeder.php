<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * The single vehicle in the collection today. Its name is a proper noun and
     * is shared by both locales; the descriptive copy arrives with the content
     * migration, so no translation rows are seeded here.
     */
    public function run(): void
    {
        Vehicle::updateOrCreate(
            ['slug' => 'denza-d9'],
            [
                'name' => 'Denza D9',
                'status' => 'active',
                'sort_order' => 0,
            ]
        );
    }
}
