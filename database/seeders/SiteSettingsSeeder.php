<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Company details already published on the site. Social profiles are stored
     * as handles only — no profile URL is invented here.
     *
     * @var array<int, array{group: string, key: string, value: string, type: string}>
     */
    private const SETTINGS = [
        ['group' => 'company', 'key' => 'company_name', 'value' => 'PT Dwimuria Investama Properti', 'type' => 'string'],
        ['group' => 'contact', 'key' => 'phone', 'value' => '0811-1234-1234', 'type' => 'string'],
        ['group' => 'contact', 'key' => 'email', 'value' => 'info@luxandgo.com', 'type' => 'string'],
        ['group' => 'social', 'key' => 'instagram_handle', 'value' => '@luxandgo', 'type' => 'string'],
        ['group' => 'social', 'key' => 'tiktok_handle', 'value' => '@luxandgo', 'type' => 'string'],
        [
            'group' => 'company',
            'key' => 'head_office_address',
            'value' => "Gajah Mada Tower, Lt. 19-01\nJl. Gajah Mada No.19-26\nPetojo Utara, Gambir\nJakarta Pusat 10130",
            'type' => 'text',
        ],
        ['group' => 'company', 'key' => 'head_office_city', 'value' => 'Jakarta Pusat', 'type' => 'string'],
    ];

    public function run(): void
    {
        foreach (self::SETTINGS as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'group' => $setting['group'],
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                ]
            );
        }
    }
}
