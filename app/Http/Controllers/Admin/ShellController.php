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

    /**
     * Renders every reusable CMS component on one page so they can be checked
     * together. Local only — it is a development aid, not a feature, and it is
     * deliberately absent from the sidebar.
     */
    public function components(): View
    {
        abort_unless(app()->environment('local', 'testing'), 404);

        return view('admin.components.index');
    }

    public function profile(): View
    {
        return view('admin.profile.index');
    }
}
