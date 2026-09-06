@extends('admin.layouts.app')

@section('title', 'Profile')

@section('content')

    <dl class="admin-detail">
        <div class="admin-detail__row">
            <dt class="admin-detail__label">Name</dt>
            <dd class="admin-detail__value">{{ auth()->user()->name }}</dd>
        </div>

        <div class="admin-detail__row">
            <dt class="admin-detail__label">Email</dt>
            <dd class="admin-detail__value">{{ auth()->user()->email }}</dd>
        </div>

        <div class="admin-detail__row">
            <dt class="admin-detail__label">Role</dt>
            <dd class="admin-detail__value">{{ ucfirst(auth()->user()->role) }}</dd>
        </div>
    </dl>

    <p class="admin-empty__note">Editing your profile arrives in a later stage.</p>

@endsection
