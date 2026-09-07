@extends('admin.layouts.app')

@section('title', 'Vehicles')

@section('content')

    <nav class="admin-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.content') }}">Content</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('admin.content.page', 'collection') }}">Our Collection</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Vehicles</span>
    </nav>

    <x-admin.ui.section-header
        title="Vehicles"
        description="The vehicles in the collection, in the order the public page shows them. An inactive vehicle keeps its content and gallery but is not shown."
    />

    <div class="admin-panel">
        <a class="admin-button admin-button--primary" href="{{ route('admin.vehicles.create') }}">Add vehicle</a>
    </div>

    <div class="admin-panel">
        @if ($vehicles->isEmpty())
            <x-admin.ui.empty-state
                title="No vehicles yet"
                copy="Add the first vehicle to build the collection."
            />
        @else
            <ul class="admin-sections">
                @foreach ($vehicles as $vehicle)
                    <li class="admin-sections__item">
                        <div class="admin-sections__body">
                            <span class="admin-vehicle__thumb">
                                @if ($vehicle->mainMedia?->exists())
                                    <img src="{{ $vehicle->mainMedia->url() }}" alt="" loading="lazy">
                                @else
                                    <span class="admin-vehicle__thumb-empty">No image</span>
                                @endif
                            </span>

                            <div>
                                <p class="admin-sections__name">{{ $vehicle->name }}</p>
                                <p class="admin-help"><code>/{{ $vehicle->slug }}</code></p>
                            </div>

                            <span class="admin-pill{{ $vehicle->isActive() ? ' admin-pill--on' : '' }}">
                                {{ $vehicle->isActive() ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="admin-sections__actions">
                            {{-- Order is changed a step at a time, so it never
                                 depends on a script being available. --}}
                            <form method="POST" action="{{ route('admin.vehicles.move', $vehicle) }}">
                                @csrf
                                <input type="hidden" name="direction" value="up">
                                <button type="submit" class="admin-button admin-button--quiet" @disabled($loop->first) aria-label="Move up">&uarr;</button>
                            </form>

                            <form method="POST" action="{{ route('admin.vehicles.move', $vehicle) }}">
                                @csrf
                                <input type="hidden" name="direction" value="down">
                                <button type="submit" class="admin-button admin-button--quiet" @disabled($loop->last) aria-label="Move down">&darr;</button>
                            </form>

                            <form method="POST" action="{{ route('admin.vehicles.status', $vehicle) }}">
                                @csrf
                                <input type="hidden" name="status" value="{{ $vehicle->isActive() ? 'inactive' : 'active' }}">
                                <button type="submit" class="admin-button admin-button--quiet">
                                    {{ $vehicle->isActive() ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            <a class="admin-button admin-button--ghost" href="{{ route('admin.vehicles.edit', $vehicle) }}">Edit</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

@endsection
