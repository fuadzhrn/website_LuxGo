<?php

namespace App\Http\Requests\Admin;

use App\Models\Page;
use App\Models\PageSection;
use App\Support\MembershipValues;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

/**
 * Rules are built from the section definition, so a section can never accept a
 * field the front end does not render, and adding a page means editing config
 * rather than writing another request class.
 */
class UpdatePageSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /* The route already runs behind auth + administrator; this only asserts
           that the section really belongs to the page in the URL. */
        return $this->section()->page_id === $this->page()->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $definition = $this->definition();
        $rules = ['is_active' => ['required', 'boolean']];

        foreach (config('locales.supported') as $locale) {
            foreach (Arr::get($definition, 'fields', []) as $path => $field) {
                $rules["content.{$locale}.{$path}"] = array_merge(
                    $field['rules'] ?? ['nullable', 'string', 'max:255'],
                    [$this->placeholderRule()],
                );
            }
        }

        foreach (Arr::get($definition, 'settings', []) as $key => $setting) {
            $rules["settings.{$key}"] = match ($setting['type'] ?? null) {
                'cta' => ['required', Rule::in(array_keys(config('page_content.cta_targets', [])))],
                'vehicle' => ['nullable', 'integer', 'exists:vehicles,id'],
                default => ['nullable', 'string', 'max:255'],
            };
        }

        foreach (array_keys(Arr::get($definition, 'media', [])) as $slot) {
            $rules["media_{$slot}"] = array_merge(['nullable', 'file'], config('admin.images.rules'));
            $rules["media_{$slot}_media_id"] = ['nullable', 'integer', 'exists:media,id'];
            $rules["media_{$slot}_remove"] = ['nullable', 'boolean'];
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
            foreach (Arr::get($this->definition(), 'fields', []) as $path => $field) {
                $attributes["content.{$locale}.{$path}"] = ($field['label'] ?? $path).' ('.($labels[$locale] ?? $locale).')';
            }
        }

        return $attributes;
    }

    /**
     * Copy may refer to a business figure by placeholder, but only to one the
     * site knows how to fill in — a typo is caught here rather than shipped to
     * the page as literal braces.
     */
    private function placeholderRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! is_string($value)) {
                return;
            }

            preg_match_all('/\{\{[^}]*\}\}/', $value, $matches);

            foreach ($matches[0] as $token) {
                if (! in_array($token, MembershipValues::allowedPlaceholders(), true)) {
                    $fail("The :attribute uses an unknown placeholder {$token}.");
                }
            }
        };
    }

    public function page(): Page
    {
        return $this->route('page');
    }

    public function section(): PageSection
    {
        return $this->route('section');
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return config("page_content.pages.{$this->page()->key}.sections.{$this->section()->section_key}", []);
    }
}
