@extends('admin.layouts.app')

@section('title', 'Business settings')

@section('content')

    <nav class="admin-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.content') }}">Content</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('admin.content.page', $page) }}">{{ $definition['label'] }}</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Business settings</span>
    </nav>

    <x-admin.ui.section-header
        title="Business settings"
        description="The published membership figures. They are stored once here and used by every section, the calculator and the FAQ — the same in both languages."
    />

    <form method="POST" action="{{ route('admin.content.business-settings.update', $page) }}">
        @csrf
        @method('PUT')

        <div class="admin-panel">
            @foreach ($fields as $name => $field)
                <x-admin.form.input
                    :name="$name"
                    :label="$field['label']"
                    type="number"
                    :value="$settings->{$name}"
                    :help="$field['help'] ?? null"
                    :min="$field['min']"
                    step="1"
                    required
                />
            @endforeach
        </div>

        <div class="admin-panel">
            <x-admin.ui.section-header
                title="Calculated from the figures above"
                description="These are worked out, never stored — change a figure and they follow."
            />

            <dl class="admin-detail">
                <div class="admin-detail__row">
                    <dt class="admin-detail__label">Total usage rights (1 LOT)</dt>
                    <dd class="admin-detail__value">{{ $values->totalMembershipRights() }}</dd>
                </div>
                <div class="admin-detail__row">
                    <dt class="admin-detail__label">Additional usage total</dt>
                    <dd class="admin-detail__value">{{ $values->additionalUsageTotalFormatted() }}</dd>
                </div>
                <div class="admin-detail__row">
                    <dt class="admin-detail__label">Regular price</dt>
                    <dd class="admin-detail__value">{{ $values->regularPrice() }}</dd>
                </div>
                <div class="admin-detail__row">
                    <dt class="admin-detail__label">Promo price</dt>
                    <dd class="admin-detail__value">{{ $values->promoPrice() }}</dd>
                </div>
            </dl>

            {{-- The tokens copy may use, so an editor never types a figure. --}}
            <p class="admin-fieldgroup">Placeholders available in the section copy</p>

            <ul class="admin-tokens">
                @foreach (App\Support\MembershipValues::allowedPlaceholders() as $token)
                    <li><code>{{ $token }}</code></li>
                @endforeach
            </ul>
        </div>

        <x-admin.ui.save-bar :updated-at="$settings->updated_at">
            <a class="admin-button admin-button--quiet" href="{{ route('admin.content.page', $page) }}">Back to sections</a>
        </x-admin.ui.save-bar>
    </form>

@endsection
