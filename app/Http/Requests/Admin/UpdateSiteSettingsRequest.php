<?php

namespace App\Http\Requests\Admin;

use App\Http\Controllers\Admin\SiteSettingsController;
use Illuminate\Foundation\Http\FormRequest;

/**
 * The rules come from the same field list the form is built from, so a field
 * can never be saved without being validated.
 */
class UpdateSiteSettingsRequest extends FormRequest
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
        $rules = [];

        foreach (SiteSettingsController::FIELDS as $key => $field) {
            $rules[$key] = $field['rules'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [];

        foreach (SiteSettingsController::FIELDS as $key => $field) {
            $attributes[$key] = strtolower($field['label']);
        }

        return $attributes;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.regex' => 'The phone number may only contain digits, spaces and the characters + ( ) - .',
            'instagram_handle.regex' => 'The Instagram handle may only contain letters, numbers, dots and underscores.',
            'tiktok_handle.regex' => 'The TikTok handle may only contain letters, numbers, dots and underscores.',
        ];
    }
}
