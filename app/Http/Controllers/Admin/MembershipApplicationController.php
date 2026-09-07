<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateApplicationStatusRequest;
use App\Models\MembershipApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * The enquiries submitted through the public membership form.
 *
 * A lead is a record of what someone sent, so nothing here edits it: the only
 * thing an administrator changes is how far along the follow-up is. There is
 * deliberately no delete — the history stays.
 */
class MembershipApplicationController extends Controller
{
    private const PER_PAGE = 25;

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = $this->validValue($request->query('status'), array_keys(MembershipApplication::statusOptions()));
        $locale = $this->validValue($request->query('locale'), config('locales.supported'));

        $applications = MembershipApplication::query()
            ->when($search !== '', function ($query) use ($search) {
                /* Bound parameters, never string-interpolated SQL. */
                $query->where(function ($match) use ($search) {
                    $match->where('full_name', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->when($status !== null, fn ($query) => $query->where('status', $status))
            ->when($locale !== null, fn ($query) => $query->where('locale', $locale))
            /* Newest first, falling back to the row's own timestamp for
               submissions recorded before submitted_at was filled in. */
            ->orderByRaw('COALESCE(submitted_at, created_at) desc')
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('admin.applications.index', [
            'applications' => $applications,
            'search' => $search,
            'status' => $status,
            'locale' => $locale,
            'counts' => $this->counts(),
        ]);
    }

    public function show(MembershipApplication $application): View
    {
        return view('admin.applications.show', [
            'application' => $application,
        ]);
    }

    public function updateStatus(UpdateApplicationStatusRequest $request, MembershipApplication $application): RedirectResponse
    {
        /* The status is the only field this module writes. */
        $application->update(['status' => $request->validated('status')]);

        return redirect()
            ->route('admin.applications.show', $application)
            ->with('success', 'Application status updated successfully.');
    }

    /**
     * One grouped query for the summary line, rather than a count per status.
     *
     * @return array<string, int>
     */
    private function counts(): array
    {
        $byStatus = MembershipApplication::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        return [
            'total' => array_sum($byStatus),
            'new' => $byStatus['new'] ?? 0,
            'contacted' => $byStatus['contacted'] ?? 0,
            'in_progress' => $byStatus['in_progress'] ?? 0,
        ];
    }

    /**
     * Filters only ever take a value the application knows; anything else is
     * treated as "no filter" rather than reaching the query.
     *
     * @param  array<int, string>  $allowed
     */
    private function validValue(mixed $value, array $allowed): ?string
    {
        return is_string($value) && in_array($value, $allowed, true) ? $value : null;
    }
}
