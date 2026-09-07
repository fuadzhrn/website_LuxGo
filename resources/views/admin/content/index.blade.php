@extends('admin.layouts.app')

@section('title', 'Content')

@section('content')

    <p class="admin-lede">The six public pages. Each one is edited section by section, in both languages.</p>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th scope="col">Page</th>
                    <th scope="col">Slug</th>
                    <th scope="col">Sections</th>
                    <th scope="col">Status</th>
                    <th scope="col"><span class="admin-visually-hidden">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pages as $page)
                    @php($isEditable = (bool) config('page_content.pages.'.$page->key.'.editable', false))
                    <tr>
                        <td>{{ config('page_content.pages.'.$page->key.'.label', Str::headline($page->key)) }}</td>
                        <td><code>/{{ $page->slug ?? '' }}</code></td>
                        <td>{{ $page->sections_count }}</td>
                        <td>
                            <span class="admin-pill{{ $page->is_active ? ' admin-pill--on' : '' }}">
                                {{ $page->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </td>
                        <td class="admin-table__actions">
                            @if ($isEditable)
                                <a class="admin-button admin-button--ghost" href="{{ route('admin.content.page', $page) }}">Edit content</a>
                            @else
                                <span class="admin-help">No editor</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection
