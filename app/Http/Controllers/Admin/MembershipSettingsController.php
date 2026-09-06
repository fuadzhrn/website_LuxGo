<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipSetting;
use App\Models\Page;
use App\Services\PageContentService;
use App\Support\MembershipValues;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The published membership figures. They are stored once, here, and every page,
 * the calculator and the FAQ read them from this single row — so a change lands
 * everywhere at once and no copy can go stale.
 *
 * There is deliberately no additional LOT price: that figure has not been
 * published, so the site does not claim one.
 */
class MembershipSettingsController extends Controller
{
    /**
     * Each figure with the smallest value that still makes sense. Prices and
     * fees may be zero; counts and durations may not.
     *
     * @var array<string, array{label: string, min: int, help?: string}>
     */
    private const FIELDS = [
        'regular_membership_price' => ['label' => 'Regular membership price', 'min' => 0, 'help' => 'In rupiah, digits only. Formatted for display.'],
        'promo_membership_price' => ['label' => 'Promo membership price', 'min' => 0],
        'promo_member_limit' => ['label' => 'Promo member limit', 'min' => 1],
        'membership_period_years' => ['label' => 'Membership period (years)', 'min' => 1],
        'base_usage_rights_per_year' => ['label' => 'Base usage rights per year', 'min' => 1],
        'additional_lot_rights_per_year' => ['label' => 'Additional LOT rights per year', 'min' => 0],
        'member_usage_fee' => ['label' => 'Member usage fee', 'min' => 0],
        'additional_usage_fee' => ['label' => 'Additional usage fee', 'min' => 0],
        'usage_duration_hours' => ['label' => 'Usage duration (hours)', 'min' => 1],
    ];

    public function __construct(private readonly PageContentService $content) {}

    public function edit(Page $page): View
    {
        $this->assertHasSettings($page);

        $settings = MembershipSetting::query()->orderBy('id')->firstOrFail();

        return view('admin.content.business-settings', [
            'page' => $page,
            'definition' => $this->content->definition($page->key) ?? [],
            'settings' => $settings,
            'fields' => self::FIELDS,
            'values' => new MembershipValues($settings),
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $this->assertHasSettings($page);

        $rules = [];

        foreach (self::FIELDS as $name => $field) {
            $rules[$name] = ['required', 'integer', 'min:'.$field['min']];
        }

        $validated = $request->validate($rules, [], array_map(
            fn (array $field) => strtolower($field['label']),
            self::FIELDS
        ));

        $settings = MembershipSetting::query()->orderBy('id')->firstOrFail();

        /* One row, one update: nothing else needs changing for the page, the
           calculator and the FAQ to follow. */
        $settings->update($validated);

        return redirect()
            ->route('admin.content.business-settings', $page)
            ->with('success', 'Changes saved successfully.');
    }

    private function assertHasSettings(Page $page): void
    {
        abort_unless($this->content->isEditable($page->key), 404);
        abort_unless((bool) config("page_content.pages.{$page->key}.business_settings", false), 404);
    }
}
