<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\PageSection;
use App\Models\Vehicle;
use App\Services\MediaService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

/**
 * The single vehicle in the collection today, with the copy and imagery the
 * approved page already publishes. Its name is a proper noun and is shared by
 * both locales; the descriptive copy is translated.
 *
 * This is initial content, not a reset: a value the admin has saved is never
 * overwritten, an image is only attached where nothing is attached yet, and
 * running it again changes nothing.
 */
class VehicleSeeder extends Seeder
{
    /**
     * Where the vehicle's shipped imagery lives. Nothing is invented here — the
     * files are the ones the collection page already renders.
     */
    private const MAIN_IMAGE = 'assets/images/luxgo/collection/featured/denza-d9-main.webp';

    private const GALLERY = [
        'assets/images/luxgo/collection/interior/interior-main.webp',
        'assets/images/luxgo/collection/interior/interior-detail-01.webp',
        'assets/images/luxgo/collection/interior/interior-detail-02.webp',
    ];

    public function __construct(private readonly MediaService $media) {}

    public function run(): void
    {
        $vehicle = Vehicle::updateOrCreate(
            ['slug' => 'denza-d9'],
            [
                'name' => 'Denza D9',
                'status' => Vehicle::STATUS_ACTIVE,
                'sort_order' => 0,
            ]
        );

        $this->seedTranslations($vehicle);
        $this->seedMainImage($vehicle);
        $this->seedGallery($vehicle);
        $this->seedSectionReference($vehicle);
    }

    /**
     * The vehicle copy the collection page shipped with, moved into the record
     * it belongs to so it is no longer tied to one page.
     */
    private function seedTranslations(Vehicle $vehicle): void
    {
        foreach (config('locales.supported') as $locale) {
            $lines = trans('collection.featured', [], $locale);

            if (! is_array($lines)) {
                continue;
            }

            $seed = [];

            foreach (config('page_content.vehicle_fields', []) as $path => $field) {
                $value = Arr::get($lines, $field['lang'] ?? $path);

                if ($value !== null) {
                    Arr::set($seed, $path, $value);
                }
            }

            if ($seed === []) {
                continue;
            }

            $translation = $vehicle->translations()->firstOrNew(['locale' => $locale]);

            /* Existing values win, so an edit survives a re-run. */
            $translation->content = array_replace_recursive($seed, $translation->content ?? []);
            $translation->save();
        }
    }

    private function seedMainImage(Vehicle $vehicle): void
    {
        if ($vehicle->main_media_id || ! is_file(public_path(self::MAIN_IMAGE))) {
            return;
        }

        $vehicle->update(['main_media_id' => $this->mediaFor(self::MAIN_IMAGE)->id]);
    }

    private function seedGallery(Vehicle $vehicle): void
    {
        /* Only an empty gallery is filled: images the admin removed stay
           removed unless this is the very first run. */
        if ($vehicle->galleryMedia()->exists()) {
            return;
        }

        foreach (self::GALLERY as $order => $path) {
            if (! is_file(public_path($path))) {
                continue;
            }

            $vehicle->galleryMedia()->attach($this->mediaFor($path)->id, ['sort_order' => $order + 1]);
        }
    }

    /**
     * The interior section shows one vehicle's gallery. It points at the
     * vehicle rather than holding a copy of its images.
     */
    private function seedSectionReference(Vehicle $vehicle): void
    {
        $section = PageSection::query()
            ->whereHas('page', fn ($query) => $query->where('key', 'collection'))
            ->where('section_key', 'inside_experience')
            ->first();

        if (! $section || ($section->settings['vehicle_id'] ?? null)) {
            return;
        }

        $section->update(['settings' => array_merge($section->settings ?? [], ['vehicle_id' => $vehicle->id])]);
    }

    /**
     * One library record per shipped asset, however many runs point at it. The
     * original file in public/assets is left where it is.
     */
    private function mediaFor(string $path): Media
    {
        $name = basename($path);

        return Media::where('filename', $name)
            ->where('size_bytes', filesize(public_path($path)))
            ->first()
            ?? $this->media->storeFromPath(public_path($path), $name);
    }
}
