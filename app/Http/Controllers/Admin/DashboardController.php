<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MembershipApplication;
use App\Models\Page;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Counts come straight from the database — nothing here is a placeholder
     * number.
     */
    public function __invoke(): View
    {
        return view('admin.dashboard.index', [
            'pageCount' => Page::count(),
            'mediaCount' => Media::count(),
            'applicationCount' => MembershipApplication::count(),
            'newApplicationCount' => MembershipApplication::where('status', 'new')->count(),
        ]);
    }
}
