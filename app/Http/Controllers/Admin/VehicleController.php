<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VehicleRequest;
use App\Models\Media;
use App\Models\Vehicle;
use App\Services\VehicleWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

/**
 * The vehicles behind Our Collection. The record, its two translations and its
 * imagery live here; the page that shows them keeps only the copy around them.
 *
 * There is deliberately no specification system — the site publishes what the
 * collection page publishes, and nothing it has not been given.
 */
class VehicleController extends Controller
{
    public function __construct(private readonly VehicleWriter $writer) {}

    public function index(): View
    {
        return view('admin.vehicles.index', [
            'vehicles' => Vehicle::query()
                ->with(['mainMedia', 'translations'])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.vehicles.form', [
            'vehicle' => new Vehicle([
                'status' => Vehicle::STATUS_ACTIVE,
                'sort_order' => ((int) Vehicle::max('sort_order')) + 1,
            ]),
            'fields' => config('page_content.vehicle_fields', []),
            'libraryMedia' => $this->libraryMedia(),
        ]);
    }

    public function store(VehicleRequest $request): RedirectResponse
    {
        try {
            $vehicle = $this->writer->save(
                new Vehicle,
                $request->validated(),
                $request->file('media_main_image'),
                $request->user()->id,
            );
        } catch (Throwable) {
            return back()->withInput()->with('error', 'Unable to save this vehicle. No changes were made.');
        }

        return redirect()
            ->route('admin.vehicles.edit', $vehicle)
            ->with('success', 'Changes saved successfully.');
    }

    public function edit(Vehicle $vehicle): View
    {
        return view('admin.vehicles.form', [
            'vehicle' => $vehicle,
            'fields' => config('page_content.vehicle_fields', []),
            'libraryMedia' => $this->libraryMedia(),
        ]);
    }

    public function update(VehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        try {
            $this->writer->save(
                $vehicle,
                $request->validated(),
                $request->file('media_main_image'),
                $request->user()->id,
            );
        } catch (Throwable) {
            return back()->withInput()->with('error', 'Unable to save this vehicle. No changes were made.');
        }

        return redirect()
            ->route('admin.vehicles.edit', $vehicle)
            ->with('success', 'Changes saved successfully.');
    }

    /**
     * Switches a vehicle on or off. Nothing is removed: an inactive vehicle
     * keeps its copy, its gallery and its place in the order.
     */
    public function status(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update([
            'status' => $request->input('status') === Vehicle::STATUS_ACTIVE
                ? Vehicle::STATUS_ACTIVE
                : Vehicle::STATUS_INACTIVE,
        ]);

        return redirect()
            ->route('admin.vehicles')
            ->with('success', $vehicle->isActive() ? 'Vehicle activated.' : 'Vehicle deactivated.');
    }

    /**
     * Moves a vehicle one place up or down, then rewrites the whole order so
     * the public page can rely on sort_order alone.
     */
    public function move(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $ordered = Vehicle::query()->orderBy('sort_order')->orderBy('id')->get()->all();
        $index = null;

        foreach ($ordered as $position => $item) {
            if ($item->is($vehicle)) {
                $index = $position;
            }
        }

        $target = $request->input('direction') === 'up' ? $index - 1 : $index + 1;

        if ($index === null || ! isset($ordered[$target])) {
            return redirect()->route('admin.vehicles');
        }

        [$ordered[$index], $ordered[$target]] = [$ordered[$target], $ordered[$index]];

        DB::transaction(function () use ($ordered) {
            foreach ($ordered as $position => $item) {
                $item->update(['sort_order' => $position + 1]);
            }
        });

        return redirect()->route('admin.vehicles');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        if ($vehicle->isInUse()) {
            return redirect()
                ->route('admin.vehicles.edit', $vehicle)
                ->with('error', 'This vehicle is currently in use. Deactivate it or change the referenced vehicle before deleting.');
        }

        try {
            $this->writer->delete($vehicle);
        } catch (Throwable) {
            return back()->with('error', 'Unable to delete this vehicle.');
        }

        return redirect()
            ->route('admin.vehicles')
            ->with('success', 'Vehicle deleted. Its images are still in the media library.');
    }

    /**
     * The library, for picking a gallery image that is already uploaded.
     *
     * @return array<int, string>
     */
    private function libraryMedia(): array
    {
        return Media::query()->latest()->limit(60)->pluck('filename', 'id')->all();
    }

    /* Gallery -------------------------------------------------------------- */

    public function addGalleryImage(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validate([
            'media_id' => ['nullable', 'integer', 'exists:media,id'],
            'file' => array_merge(['nullable', 'file'], config('admin.images.rules')),
        ]);

        try {
            $this->writer->addGalleryImage(
                $vehicle,
                isset($validated['media_id']) ? (int) $validated['media_id'] : null,
                $request->file('file'),
                $request->user()->id,
            );
        } catch (Throwable) {
            return back()->with('error', 'Unable to add this image.');
        }

        return redirect()
            ->route('admin.vehicles.edit', $vehicle)
            ->with('success', 'Gallery updated.');
    }

    public function removeGalleryImage(Vehicle $vehicle, int $media): RedirectResponse
    {
        /* Detaches only: the file stays in the library for use elsewhere. */
        $this->writer->removeGalleryImage($vehicle, $media);

        return redirect()
            ->route('admin.vehicles.edit', $vehicle)
            ->with('success', 'Image removed from the gallery. It is still in the media library.');
    }

    public function moveGalleryImage(Request $request, Vehicle $vehicle, int $media): RedirectResponse
    {
        $this->writer->moveGalleryImage(
            $vehicle,
            $media,
            $request->input('direction') === 'up' ? 'up' : 'down',
        );

        return redirect()->route('admin.vehicles.edit', $vehicle);
    }
}
