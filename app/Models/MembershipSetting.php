<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipSetting extends Model
{
    use HasFactory;

    /**
     * Shared business numbers. There is no additional LOT price here because
     * that figure has not been published.
     */
    protected $fillable = [
        'regular_membership_price',
        'promo_membership_price',
        'promo_member_limit',
        'membership_period_years',
        'base_usage_rights_per_year',
        'additional_lot_rights_per_year',
        'member_usage_fee',
        'additional_usage_fee',
        'usage_duration_hours',
    ];

    protected function casts(): array
    {
        return [
            'regular_membership_price' => 'integer',
            'promo_membership_price' => 'integer',
            'promo_member_limit' => 'integer',
            'membership_period_years' => 'integer',
            'base_usage_rights_per_year' => 'integer',
            'additional_lot_rights_per_year' => 'integer',
            'member_usage_fee' => 'integer',
            'additional_usage_fee' => 'integer',
            'usage_duration_hours' => 'integer',
        ];
    }

    /**
     * Usage Rights per year for a given number of LOTs — the same rule the
     * front-end calculator applies.
     */
    public function annualRightsFor(int $lots): int
    {
        $lots = max(1, $lots);

        return $this->base_usage_rights_per_year
            + (($lots - 1) * $this->additional_lot_rights_per_year);
    }

    public function totalRightsFor(int $lots): int
    {
        return $this->annualRightsFor($lots) * $this->membership_period_years;
    }
}
