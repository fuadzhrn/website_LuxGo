<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSiteSettingsRequest;
use App\Models\SiteSetting;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * The company details the whole site shows: the footer on every page, the
 * contact section and the legal pages all read them from here, so each value is
 * written once.
 *
 * Links are never stored — a tel: link and a social profile URL are built from
 * the number and the handle, so they cannot drift apart from what is displayed.
 */
class SiteSettingsController extends Controller
{
    /**
     * The settings this screen manages, with where each belongs and how it is
     * validated. The list is here so the form, the rules and the save all agree.
     *
     * @var array<string, array{label: string, group: string, type: string, rules: array<int, string>, help?: string, textarea?: bool}>
     */
    public const FIELDS = [
        'company_name' => [
            'label' => 'Company name',
            'group' => 'company',
            'type' => 'string',
            'rules' => ['required', 'string', 'max:120'],
            'help' => 'Shown in the footer and on the contact section.',
        ],
        'phone' => [
            'label' => 'WhatsApp / phone',
            'group' => 'contact',
            'type' => 'string',
            'rules' => ['required', 'string', 'max:40', 'regex:/^[0-9+][0-9 ()\-.]*$/'],
            'help' => 'Written the way it should be read, for example 0811-1234-1234. The dialling link is built from it.',
        ],
        'email' => [
            'label' => 'Email',
            'group' => 'contact',
            'type' => 'string',
            'rules' => ['required', 'email', 'max:120'],
        ],
        'instagram_handle' => [
            'label' => 'Instagram handle',
            'group' => 'social',
            'type' => 'string',
            'rules' => ['nullable', 'string', 'max:60', 'regex:/^@?[A-Za-z0-9._]+$/'],
            'help' => 'The handle only, for example @luxandgo.id. Leave empty to hide the link.',
        ],
        'tiktok_handle' => [
            'label' => 'TikTok handle',
            'group' => 'social',
            'type' => 'string',
            'rules' => ['nullable', 'string', 'max:60', 'regex:/^@?[A-Za-z0-9._]+$/'],
            'help' => 'The handle only, for example @luxandgo.id. Leave empty to hide the link.',
        ],
        'head_office_address' => [
            'label' => 'Head office address',
            'group' => 'company',
            'type' => 'text',
            'textarea' => true,
            'rules' => ['required', 'string', 'max:400'],
            'help' => 'One line per row. The contact section prints them as written.',
        ],
        'head_office_city' => [
            'label' => 'Head office city',
            'group' => 'company',
            'type' => 'string',
            'rules' => ['required', 'string', 'max:80'],
            'help' => 'The short form shown in the footer.',
        ],
    ];

    public function edit(SiteSettings $settings): View
    {
        return view('admin.settings.index', [
            'fields' => self::FIELDS,
            'values' => SiteSetting::query()->pluck('value', 'key')->all(),
            'settings' => $settings,
            'updatedAt' => SiteSetting::query()->max('updated_at'),
        ]);
    }

    public function update(UpdateSiteSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            foreach (self::FIELDS as $key => $field) {
                $value = $validated[$key] ?? '';

                /* A handle is stored with its @ however the admin typed it, so
                   the site shows one consistent form. */
                if (Str::endsWith($key, '_handle') && $value !== '') {
                    $value = '@'.ltrim(trim($value), '@');
                }

                SiteSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => trim((string) $value), 'group' => $field['group'], 'type' => $field['type']]
                );
            }
        });

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Settings updated successfully.');
    }
}
