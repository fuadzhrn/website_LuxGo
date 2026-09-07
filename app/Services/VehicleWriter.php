<?php

namespace App\Services;

use App\Models\Media;
use App\Models\Vehicle;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Saves a vehicle: the shared record, both translations and the main image, in
 * one transaction. The gallery is managed separately, a step at a time, so its
 * order is always the order the database holds.
 */
class VehicleWriter
{
    /**
     * Files written during this save, cleared away if the transaction rolls back.
     *
     * @var array<int, array{0: string, 1: string}>
     */
    private array $storedFiles = [];

    public function __construct(private readonly MediaService $media) {}

    /**
     * @param  array<string, mixed>  $data  validated input
     */
    public function save(Vehicle $vehicle, array $data, ?UploadedFile $mainImage = null, ?int $userId = null): Vehicle
    {
        $this->storedFiles = [];

        try {
            return DB::transaction(function () use ($vehicle, $data, $mainImage, $userId) {
                $vehicle->fill([
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'status' => $data['is_active'] ? Vehicle::STATUS_ACTIVE : Vehicle::STATUS_INACTIVE,
                    'sort_order' => $data['sort_order'] ?? $vehicle->sort_order ?? 0,
                    'main_media_id' => $this->mainMediaId($vehicle, $data, $mainImage, $userId),
                ])->save();

                $this->saveTranslations($vehicle, $data);

                return $vehicle;
            });
        } catch (Throwable $e) {
            /* The rollback took the media row with it, so its file must go too
               rather than linger unreferenced. */
            foreach ($this->storedFiles as [$disk, $path]) {
                Storage::disk($disk)->delete($path);
            }

            throw $e;
        }
    }

    /**
     * Adds one image to the gallery, uploading it into the library first when
     * the admin chose a file rather than an existing image.
     */
    public function addGalleryImage(Vehicle $vehicle, ?int $mediaId, ?UploadedFile $file, ?int $userId = null): void
    {
        $mediaId = $file instanceof UploadedFile
            ? $this->media->store($file, $userId)->id
            : $mediaId;

        if (! $mediaId || ! Media::whereKey($mediaId)->exists()) {
            return;
        }

        if ($vehicle->galleryMedia()->where('media_id', $mediaId)->exists()) {
            return;
        }

        $vehicle->galleryMedia()->attach($mediaId, [
            'sort_order' => ((int) $vehicle->galleryMedia()->max('sort_order')) + 1,
        ]);
    }

    /**
     * Detaches an image from the gallery. The file stays in the library — it may
     * be used elsewhere, and removing it here is not a deletion.
     */
    public function removeGalleryImage(Vehicle $vehicle, int $mediaId): void
    {
        $vehicle->galleryMedia()->detach($mediaId);

        $this->renumberGallery($vehicle);
    }

    /**
     * Moves one image up or down, then rewrites the whole order so entries that
     * share a position cannot get stuck.
     */
    public function moveGalleryImage(Vehicle $vehicle, int $mediaId, string $direction): void
    {
        $ordered = $vehicle->galleryMedia()->get()->all();
        $index = null;

        foreach ($ordered as $position => $media) {
            if ($media->getKey() === $mediaId) {
                $index = $position;
            }
        }

        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($index === null || ! isset($ordered[$target])) {
            return;
        }

        [$ordered[$index], $ordered[$target]] = [$ordered[$target], $ordered[$index]];

        DB::transaction(function () use ($vehicle, $ordered) {
            foreach ($ordered as $position => $media) {
                $vehicle->galleryMedia()->updateExistingPivot($media->getKey(), ['sort_order' => $position + 1]);
            }
        });
    }

    /**
     * Deletes the vehicle and its translations, and detaches its gallery. No
     * file leaves the media library: the images may be used elsewhere.
     */
    public function delete(Vehicle $vehicle): void
    {
        DB::transaction(function () use ($vehicle) {
            $vehicle->galleryMedia()->detach();
            $vehicle->translations()->delete();
            $vehicle->delete();
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function mainMediaId(Vehicle $vehicle, array $data, ?UploadedFile $file, ?int $userId): ?int
    {
        if ((bool) ($data['media_main_image_remove'] ?? false)) {
            return null;
        }

        if ($file instanceof UploadedFile) {
            $uploaded = $this->media->store($file, $userId);
            $this->storedFiles[] = [$uploaded->disk, $uploaded->path];

            return $uploaded->id;
        }

        $mediaId = $data['media_main_image_media_id'] ?? null;

        if ($mediaId && Media::whereKey($mediaId)->exists()) {
            return (int) $mediaId;
        }

        return $vehicle->main_media_id;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function saveTranslations(Vehicle $vehicle, array $data): void
    {
        foreach (config('locales.supported') as $locale) {
            $translation = $vehicle->translations()->firstOrNew(['locale' => $locale]);
            $content = $translation->content ?? [];

            /* Only the defined fields are written, into a copy of what is
               already stored, so an unknown key is preserved rather than lost. */
            foreach (array_keys(config('page_content.vehicle_fields', [])) as $path) {
                Arr::set($content, $path, Arr::get($data, "content.{$locale}.{$path}"));
            }

            $translation->content = $content;
            $translation->save();
        }
    }

    private function renumberGallery(Vehicle $vehicle): void
    {
        foreach ($vehicle->galleryMedia()->get() as $position => $media) {
            $vehicle->galleryMedia()->updateExistingPivot($media->getKey(), ['sort_order' => $position + 1]);
        }
    }
}
