@extends('admin.layouts.app')

@section('title', $application->full_name)

@section('content')

    <nav class="admin-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.applications') }}">Membership applications</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">{{ $application->full_name }}</span>
    </nav>

    <x-admin.ui.section-header
        title="Application detail"
        description="What the applicant sent. Only the follow-up status is edited here."
    />

    <div class="admin-panel">
        <p class="admin-label">Status</p>
        <x-admin.ui.status-badge :status="$application->status" :label="$application->statusLabel()" />
    </div>

    <div class="admin-panel">
        <x-admin.ui.section-header title="Contact information" />

        <dl class="admin-detail">
            <div class="admin-detail__row">
                <dt class="admin-detail__label">Full name</dt>
                <dd class="admin-detail__value">{{ $application->full_name }}</dd>
            </div>

            <div class="admin-detail__row">
                <dt class="admin-detail__label">WhatsApp</dt>
                <dd class="admin-detail__value">
                    {{-- The number is shown as it was submitted; only the link
                         is built from it. --}}
                    @if ($application->phoneLink())
                        <a href="{{ $application->phoneLink() }}">{{ $application->phone }}</a>
                    @else
                        {{ $application->phone }}
                    @endif
                </dd>
            </div>

            <div class="admin-detail__row">
                <dt class="admin-detail__label">Email</dt>
                <dd class="admin-detail__value">
                    @if ($application->emailLink())
                        <a href="{{ $application->emailLink() }}">{{ $application->email }}</a>
                    @else
                        {{ $application->email }}
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    <div class="admin-panel">
        <x-admin.ui.section-header title="Membership interest" />

        <dl class="admin-detail">
            <div class="admin-detail__row">
                <dt class="admin-detail__label">LOT interested</dt>
                <dd class="admin-detail__value">{{ $application->lots_interested }}</dd>
            </div>

            <div class="admin-detail__row">
                <dt class="admin-detail__label">Language</dt>
                <dd class="admin-detail__value">{{ $application->localeLabel() }}</dd>
            </div>

            <div class="admin-detail__row">
                <dt class="admin-detail__label">Submitted</dt>
                <dd class="admin-detail__value">{{ $application->submittedAt()?->format('d M Y, H:i') ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    <div class="admin-panel">
        <x-admin.ui.section-header title="Message" />

        {{-- Escaped: the applicant's own words, never markup. --}}
        <p class="admin-message">{{ $application->message ?: 'No message provided.' }}</p>
    </div>

    <form method="POST" action="{{ route('admin.applications.status', $application) }}">
        @csrf
        @method('PATCH')

        <div class="admin-panel">
            <x-admin.ui.section-header
                title="Follow-up status"
                description="The only thing this screen changes. The submission itself stays as it was sent."
            />

            <x-admin.form.select
                name="status"
                label="Status"
                :options="App\Models\MembershipApplication::statusOptions()"
                :value="$application->status"
                required
            />
        </div>

        <x-admin.ui.save-bar label="Save status" :updated-at="$application->updated_at">
            <a class="admin-button admin-button--quiet" href="{{ route('admin.applications') }}">Back to applications</a>
        </x-admin.ui.save-bar>
    </form>

@endsection
