<?php

namespace App\Http\Requests\Admin;

use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * The translated fields come from config/page_content.php, so what a vehicle may
 * hold is described in one place rather than repeated here.
 */
class VehicleRequest extends FormRequest
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
        $vehicle = $this->route('vehicle');

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'required', 'string', 'max:120',
                /* A slug is part of a URL, so only the characters a URL wants. */
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('vehicles', 'slug')->ignore($vehicle instanceof Vehicle ? $vehicle->id : null),
            ],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'media_main_image' => array_merge(['nullable', 'file'], config('admin.images.rules')),
            'media_main_image_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'media_main_image_remove' => ['nullable', 'boolean'],
        ];

        foreach (config('locales.supported') as $locale) {
            foreach (config('page_content.vehicle_fields', []) as $path => $field) {
                $rules["content.{$locale}.{$path}"] = $field['rules'] ?? ['nullable', 'string', 'max:255'];
            }
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

        foreach (config('locales.supported') as $locale) {
            foreach (config('page_content.vehicle_fields', []) as $path => $field) {
                $attributes["content.{$locale}.{$path}"] = ($field['label'] ?? $path).' ('.($labels[$locale] ?? $locale).')';
            }
        }

        return $attributes;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers and hyphens.',
        ];
    }
}
