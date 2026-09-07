<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * SEO is plain text: a title, a description and their sharing variants. The
 * page being edited comes from the route, never from the request, so no
 * submission can point a record at a different page.
 */
class UpdateSeoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'is_indexable' => ['required', 'boolean'],
            'media_og_image' => array_merge(['nullable', 'file'], config('admin.images.rules')),
            'media_og_image_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'media_og_image_remove' => ['nullable', 'boolean'],
        ];

        /* Only the supported locales are accepted; anything else is not a
           language this site publishes. */
        foreach (config('locales.supported') as $locale) {
            $rules["seo.{$locale}.meta_title"] = ['nullable', 'string', 'max:120'];
            $rules["seo.{$locale}.meta_description"] = ['nullable', 'string', 'max:320'];
            $rules["seo.{$locale}.og_title"] = ['nullable', 'string', 'max:120'];
            $rules["seo.{$locale}.og_description"] = ['nullable', 'string', 'max:320'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [];
        $labels = config('admin.locale_labels');
        $fields = [
            'meta_title' => 'SEO title',
            'meta_description' => 'meta description',
            'og_title' => 'OG title',
            'og_description' => 'OG description',
        ];

        foreach (config('locales.supported') as $locale) {
            foreach ($fields as $field => $label) {
                $attributes["seo.{$locale}.{$field}"] = $label.' ('.($labels[$locale] ?? $locale).')';
            }
        }

        return $attributes;
    }
}
