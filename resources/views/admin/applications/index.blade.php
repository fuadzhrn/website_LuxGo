@extends('admin.layouts.app')

@section('title', 'Membership Applications')

@php
    $statusOptions = App\Models\MembershipApplication::statusOptions();
    $localeLabels = config('admin.locale_labels');
    $hasFilters = $search !== '' || $status !== null || $locale !== null;
@endphp

@section('content')

    <x-admin.ui.section-header
        title="Membership applications"
        description="Manage membership enquiries submitted through the website."
    />

    @if ($counts['total'] > 0)
        {{-- Live counts, from one grouped query. --}}
        <div class="admin-stats admin-stats--compact">
            <div class="admin-stat">
                <p class="admin-stat__value">{{ $counts['total'] }}</p>
                <p class="admin-stat__label">Total</p>
            </div>
            <div class="admin-stat">
                <p class="admin-stat__value">{{ $counts['new'] }}</p>
                <p class="admin-stat__label">New</p>
            </div>
            <div class="admin-stat">
                <p class="admin-stat__value">{{ $counts['contacted'] }}</p>
                <p class="admin-stat__label">Contacted</p>
            </div>
            <div class="admin-stat">
                <p class="admin-stat__value">{{ $counts['in_progress'] }}</p>
                <p class="admin-stat__label">In progress</p>
            </div>
        </div>
    @endif

    <div class="admin-panel">
        <form method="GET" action="{{ route('admin.applications') }}" class="admin-filters">
            <div class="admin-field">
                <label class="admin-label" for="applications-search">Search</label>
                <input
                    class="admin-input"
                    type="search"
                    id="applications-search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search name, WhatsApp, or email"
                >
            </div>

            <div class="admin-field">
                <label class="admin-label" for="applications-status">Status</label>
                <select class="admin-input admin-select" id="applications-status" name="status">
                    <option value="">All statuses</option>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-field">
                <label class="admin-label" for="applications-locale">Language</label>
                <select class="admin-input admin-select" id="applications-locale" name="locale">
                    <option value="">All languages</option>
                    @foreach (config('locales.supported') as $code)
                        <option value="{{ $code }}" @selected($locale === $code)>{{ $localeLabels[$code] ?? strtoupper($code) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-filters__actions">
                <button type="submit" class="admin-button admin-button--ghost">Apply</button>

                @if ($hasFilters)
                    <a class="admin-button admin-button--quiet" href="{{ route('admin.applications') }}">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="admin-panel">
        @if ($applications->isEmpty())
            <x-admin.ui.empty-state
                :title="$hasFilters ? 'No applications match these filters' : 'No membership applications yet'"
                :copy="$hasFilters
                    ? 'Try a different search or clear the filters.'
                    : 'Applications submitted through the website will appear here.'"
            />
        @else
            <div class="admin-table-wrap">
                <table class="admin-table admin-table--leads">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">WhatsApp</th>
                            <th scope="col">Email</th>
                            <th scope="col">LOT</th>
                            <th scope="col">Language</th>
                            <th scope="col">Submitted</th>
                            <th scope="col">Status</th>
                            <th scope="col"><span class="admin-visually-hidden">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $application)
                            <tr>
                                <td>{{ $application->full_name }}</td>
                                <td class="admin-table__cell--tight">{{ $application->phone }}</td>
                                <td>{{ $application->email }}</td>
                                <td>{{ $application->lots_interested }}</td>
                                <td>{{ $application->localeLabel() }}</td>
                                <td class="admin-table__cell--tight">{{ $application->submittedAt()?->format('d M Y, H:i') ?? '—' }}</td>
                                <td class="admin-table__cell--tight">
                                    <x-admin.ui.status-badge :status="$application->status" :label="$application->statusLabel()" />
                                </td>
                                <td class="admin-table__actions">
                                    <a class="admin-button admin-button--ghost" href="{{ route('admin.applications.show', $application) }}">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="admin-media__pagination">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

@endsection
