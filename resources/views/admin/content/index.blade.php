@extends('admin.layouts.app')

@section('title', 'Content')

@section('content')

    <p class="admin-lede">The six public pages. Section editors arrive in the next CMS stage.</p>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th scope="col">Page</th>
                    <th scope="col">Slug</th>
                    <th scope="col">Sections</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pages as $page)
                    <tr>
                        <td>{{ Str::headline($page->key) }}</td>
                        <td><code>/{{ $page->slug ?? '' }}</code></td>
                        <td>{{ $page->sections_count }}</td>
                        <td>
                            <span class="admin-pill{{ $page->is_active ? ' admin-pill--on' : '' }}">
                                {{ $page->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection
