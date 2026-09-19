<?php

namespace App\Support;

use App\Models\MembershipSetting;

/**
 * The published membership figures, in the shapes the site needs them: formatted
 * for display, calculated where they can be derived, and as the placeholder map
 * the CMS copy is rendered against.
 *
 * Every number on the Membership page comes through here, so `membership_settings`
 * stays the only place a figure is stored. Nothing is duplicated into copy, the
 * calculator, or a Blade file.
 */
class MembershipValues
{
    public function __construct(public readonly MembershipSetting $settings) {}

    /* Raw values ----------------------------------------------------------- */

    public function periodYears(): int
    {
        return $this->settings->membership_period_years;
    }

    public function baseRights(): int
    {
        return $this->settings->base_usage_rights_per_year;
    }

    public function additionalLotRights(): int
    {
        return $this->settings->additional_lot_rights_per_year;
    }

    public function vehicleChangeYears(): int
    {
        return $this->settings->vehicle_change_years;
    }

    public function vehiclePeriods(): int
    {
        return $this->settings->vehiclePeriods();
    }

    public function usageDiscountPercent(): int
    {
        return $this->settings->usage_discount_percent;
    }

    public function usageHours(): int
    {
        return $this->settings->usage_duration_hours;
    }

    public function promoMemberLimit(): int
    {
        return $this->settings->promo_member_limit;
    }

    /* Calculated values ---------------------------------------------------- */

    /**
     * Usage Rights per year for a number of LOTs — the rule the page states and
     * the calculator applies.
     */
    /* The two benefits, kept apart. An additional LOT buys Discounted Usage
       Rights, not Usage Rights, so the page reports them separately rather than
       adding them into one figure that belongs to neither. */

    public function usageRightsPerYear(): int
    {
        return $this->settings->usageRightsPerYear();
    }

    public function baseDiscountedRights(): int
    {
        return $this->settings->base_discounted_rights_per_year;
    }

    public function discountedRightsFor(int $lots): int
    {
        return $this->settings->discountedRightsFor($lots);
    }

    public function totalUsageRights(): int
    {
        return $this->settings->totalUsageRights();
    }

    public function totalDiscountedRightsFor(int $lots): int
    {
        return $this->settings->totalDiscountedRightsFor($lots);
    }

    /**
     * Total Usage Rights across the membership for a single LOT. Derived, never
     * stored: base rights × period.
     */
    public function totalMembershipRights(): int
    {
        return $this->totalUsageRights();
    }

    /**
     * What one use costs once the annual rights are spent: the member fee plus
     * the additional usage fee.
     */
    public function additionalUsageTotal(): int
    {
        return $this->settings->member_usage_fee + $this->settings->additional_usage_fee;
    }

    /* Money ---------------------------------------------------------------- */

    /**
     * Rupiah as the brand writes it — the same in both locales, and never
     * converted to another currency.
     */
    public static function rupiah(int $amount): string
    {
        return 'Rp'.number_format($amount, 0, ',', '.');
    }

    public function regularPrice(): string
    {
        return self::rupiah($this->settings->regular_membership_price);
    }

    public function promoPrice(): string
    {
        return self::rupiah($this->settings->promo_membership_price);
    }

    public function memberUsageFee(): string
    {
        return self::rupiah($this->settings->member_usage_fee);
    }

    public function publicUsageFee(): string
    {
        return self::rupiah($this->settings->public_usage_fee);
    }

    public function additionalUsageFee(): string
    {
        return self::rupiah($this->settings->additional_usage_fee);
    }

    public function additionalUsageTotalFormatted(): string
    {
        return self::rupiah($this->additionalUsageTotal());
    }

    /* CMS copy ------------------------------------------------------------- */

    /**
     * The placeholders CMS copy may use. Anything outside this list is rejected
     * by the editor, and nothing here is ever evaluated as code — it is a plain
     * string replacement.
     *
     * @return array<string, string>
     */
    public function placeholders(): array
    {
        return [
            '{{regular_price}}' => $this->regularPrice(),
            '{{promo_price}}' => $this->promoPrice(),
            '{{promo_member_limit}}' => (string) $this->promoMemberLimit(),
            '{{membership_period}}' => (string) $this->periodYears(),
            '{{base_usage_rights}}' => (string) $this->baseRights(),
            '{{additional_lot_rights}}' => (string) $this->additionalLotRights(),
            '{{base_discounted_rights}}' => (string) $this->baseDiscountedRights(),
            '{{total_discounted_rights}}' => (string) $this->totalDiscountedRightsFor(1),
            '{{member_usage_fee}}' => $this->memberUsageFee(),
            '{{additional_usage_fee}}' => $this->additionalUsageFee(),
            '{{usage_duration}}' => (string) $this->usageHours(),
            '{{public_usage_fee}}' => $this->publicUsageFee(),
            '{{usage_discount_percent}}' => (string) $this->usageDiscountPercent(),
            '{{vehicle_change_years}}' => (string) $this->vehicleChangeYears(),
            '{{vehicle_periods}}' => (string) $this->vehiclePeriods(),
            '{{total_usage_rights}}' => (string) $this->totalMembershipRights(),
            '{{additional_usage_total}}' => $this->additionalUsageTotalFormatted(),
        ];
    }

    /**
     * The tokens copy may refer to.
     *
     * Static on purpose: validating a heading on any page must not depend on
     * the membership figures existing. A test asserts this list matches the
     * keys of placeholders() exactly, so the two cannot drift apart.
     *
     * @return array<int, string>
     */
    public static function placeholderTokens(): array
    {
        return [
            '{{regular_price}}', '{{promo_price}}', '{{promo_member_limit}}',
            '{{membership_period}}', '{{base_usage_rights}}', '{{additional_lot_rights}}',
            '{{base_discounted_rights}}', '{{total_discounted_rights}}',
            '{{member_usage_fee}}', '{{additional_usage_fee}}', '{{usage_duration}}',
            '{{public_usage_fee}}', '{{usage_discount_percent}}',
            '{{vehicle_change_years}}', '{{vehicle_periods}}',
            '{{total_usage_rights}}', '{{additional_usage_total}}',
        ];
    }

    /* Front end ------------------------------------------------------------ */

    /**
     * The values the calculator script needs, rendered as data attributes so the
     * script never carries a business number of its own.
     *
     * @return array<string, int>
     */
    public function calculatorAttributes(): array
    {
        return [
            'data-base-rights' => $this->baseRights(),
            'data-base-discounted' => $this->baseDiscountedRights(),
            'data-additional-rights' => $this->additionalLotRights(),
            'data-period' => $this->periodYears(),
        ];
    }
}
