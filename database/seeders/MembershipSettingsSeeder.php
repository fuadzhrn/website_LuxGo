<?php

namespace Database\Seeders;

use App\Models\MembershipSetting;
use Illuminate\Database\Seeder;

class MembershipSettingsSeeder extends Seeder
{
    /**
     * The official published figures, shared by both locales. The additional
     * LOT price is absent on purpose — it has not been published.
     */
    public function run(): void
    {
        MembershipSetting::updateOrCreate(
            ['id' => 1],
            [
                'regular_membership_price' => 35_000_000,
                'promo_membership_price' => 25_000_000,
                'promo_member_limit' => 100,
                'membership_period_years' => 5,
                'base_usage_rights_per_year' => 6,
                'additional_lot_rights_per_year' => 2,
                'member_usage_fee' => 750_000,
                'additional_usage_fee' => 500_000,
                'usage_duration_hours' => 12,
            ]
        );
    }
}
