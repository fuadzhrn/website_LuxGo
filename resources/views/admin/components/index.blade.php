@extends('admin.layouts.app')

@section('title', 'Components')

@section('content')

    <x-admin.ui.section-header
        title="CMS components"
        description="The reusable pieces every page editor is built from. This page renders them for checking — nothing here saves."
    />

    {{-- Bilingual pattern: translated text inside the tabs, shared values outside. --}}
    <div class="admin-panel">
        <x-admin.ui.section-header title="Bilingual content" />

        <x-admin.content.language-tabs id="demo-language">
            @foreach (config('locales.supported') as $index => $locale)
                <x-admin.content.language-panel :locale="$locale" id="demo-language" :active="$index === 0">
                    <x-admin.form.input
                        :name="'demo[' . $locale . '][heading]'"
                        label="Heading"
                        :value="$locale === 'id' ? 'Mobilitas Premium.' : 'Premium Mobility.'"
                    />

                    <x-admin.form.textarea
                        :name="'demo[' . $locale . '][description]'"
                        label="Description"
                        :rows="3"
                        :value="$locale === 'id'
                            ? 'Cara baru untuk bergerak — dengan kebebasan dan fleksibilitas.'
                            : 'A new way to move — with freedom and flexibility.'"
                        help="Plain text. Rich formatting is not available."
                    />

                    <x-admin.form.input
                        :name="'demo[' . $locale . '][cta_label]'"
                        label="CTA label"
                        :value="$locale === 'id' ? 'Jelajahi Membership' : 'Explore Membership'"
                    />
                </x-admin.content.language-panel>
            @endforeach
        </x-admin.content.language-tabs>
    </div>

    {{-- Shared across both languages, so it sits outside the tabs. --}}
    <div class="admin-panel">
        <x-admin.ui.section-header
            title="Shared content"
            description="Imagery and status are the same in both languages and are never entered twice."
        />

        <x-admin.content.image-field
            name="demo_image"
            label="Section image"
            alt-name="demo_image_alt"
            alt-value=""
        />

        <div class="admin-field" style="margin-top:18px">
            <x-admin.content.status-toggle name="demo_is_active" :checked="true" />
        </div>
    </div>

    {{-- Field states, side by side, so the styling stays consistent. --}}
    <div class="admin-panel">
        <x-admin.ui.section-header title="Field states" />

        <x-admin.form.input name="demo_normal" label="Normal" value="Editable value" />
        <x-admin.form.input name="demo_required" label="Required" placeholder="Type here" :required="true" />
        <x-admin.form.input name="demo_help" label="With helper text" help="Shown under the field, read out with it." />
        <x-admin.form.input name="demo_disabled" label="Disabled" value="Not editable" :disabled="true" />
    </div>

    <div class="admin-panel">
        <x-admin.ui.section-header title="Notifications" />

        <x-admin.ui.alert type="success" message="Changes saved successfully." />
        <x-admin.ui.alert type="error" message="Unable to save changes." />
        <x-admin.ui.alert type="warning" message="Some fields are still empty in English." />
    </div>

    <div class="admin-panel">
        <x-admin.ui.section-header title="Save action" />

        <x-admin.ui.save-bar :updated-at="now()" :disabled="true">
            <p class="admin-save__meta">Inert here — the real editors submit this button.</p>
        </x-admin.ui.save-bar>
    </div>

@endsection
