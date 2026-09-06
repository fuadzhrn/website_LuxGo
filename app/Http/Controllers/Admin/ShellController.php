<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\View\View;

/**
 * Navigation shells for the modules that get their editors in the next stage.
 * Nothing here writes to the database.
 */
class ShellController extends Controller
{
    public function content(): View
    {
        return view('admin.content.index', [
            'pages' => Page::withCount('sections')->orderBy('sort_order')->get(),
        ]);
    }

    public function media(): View
    {
        return view('admin.media.index');
    }

    public function applications(): View
    {
        return view('admin.applications.index');
    }

    public function seo(): View
    {
        return view('admin.seo.index');
    }

    public function settings(): View
    {
        return view('admin.settings.index');
    }

    public function profile(): View
    {
        return view('admin.profile.index');
    }
}
