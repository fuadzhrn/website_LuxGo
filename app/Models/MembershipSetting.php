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
        'vehicle_change_years',
        'base_usage_rights_per_year',
        'base_discounted_rights_per_year',
        'additional_lot_rights_per_year',
        'member_usage_fee',
        'public_usage_fee',
        'usage_discount_percent',
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
            'vehicle_change_years' => 'integer',
            'base_usage_rights_per_year' => 'integer',
            'base_discounted_rights_per_year' => 'integer',
            'additional_lot_rights_per_year' => 'integer',
            'member_usage_fee' => 'integer',
            'public_usage_fee' => 'integer',
            'usage_discount_percent' => 'integer',
            'additional_usage_fee' => 'integer',
            'usage_duration_hours' => 'integer',
        ];
    }

    /**
     * Usage Rights are a benefit of the membership, not of a LOT: they do not
     * grow with how many LOTs are held. One a year, every year of the term.
     */
    public function usageRightsPerYear(): int
    {
        return $this->base_usage_rights_per_year;
    }

    /**
     * Discounted Usage Rights come with the membership and grow with it: the
     * base amount, plus what each additional LOT adds.
     */
    public function discountedRightsFor(int $lots): int
    {
        $extra = max(0, max(1, $lots) - 1);

        return $this->base_discounted_rights_per_year
            + ($extra * $this->additional_lot_rights_per_year);
    }

    public function totalUsageRights(): int
    {
        return $this->usageRightsPerYear() * $this->membership_period_years;
    }

    public function totalDiscountedRightsFor(int $lots): int
    {
        return $this->discountedRightsFor($lots) * $this->membership_period_years;
    }

    /**
     * How many vehicles a member goes through across the term. Derived from the
     * two stored figures so the two can never disagree; a replacement interval
     * of zero means the vehicle is not replaced.
     */
    public function vehiclePeriods(): int
    {
        if ($this->vehicle_change_years < 1) {
            return 1;
        }

        return max(1, intdiv($this->membership_period_years, $this->vehicle_change_years));
    }
}
