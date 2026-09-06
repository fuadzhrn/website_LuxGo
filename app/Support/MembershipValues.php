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
    public function annualRightsFor(int $lots): int
    {
        return $this->settings->annualRightsFor($lots);
    }

    public function totalRightsFor(int $lots): int
    {
        return $this->settings->totalRightsFor($lots);
    }

    /**
     * Total Usage Rights across the membership for a single LOT. Derived, never
     * stored: base rights × period.
     */
    public function totalMembershipRights(): int
    {
        return $this->totalRightsFor(1);
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
            '{{member_usage_fee}}' => $this->memberUsageFee(),
            '{{additional_usage_fee}}' => $this->additionalUsageFee(),
            '{{usage_duration}}' => (string) $this->usageHours(),
            '{{total_usage_rights}}' => (string) $this->totalMembershipRights(),
            '{{additional_usage_total}}' => $this->additionalUsageTotalFormatted(),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function allowedPlaceholders(): array
    {
        return [
            '{{regular_price}}', '{{promo_price}}', '{{promo_member_limit}}',
            '{{membership_period}}', '{{base_usage_rights}}', '{{additional_lot_rights}}',
            '{{member_usage_fee}}', '{{additional_usage_fee}}', '{{usage_duration}}',
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
            'data-additional-rights' => $this->additionalLotRights(),
            'data-period' => $this->periodYears(),
        ];
    }
}
