<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * The single place that writes to and removes from the media library, so the
 * library page and the picker cannot drift into different upload behaviour.
 */
class MediaService
{
    public function store(UploadedFile $file, ?int $userId = null): Media
    {
        $disk = config('admin.media.disk');
        $directory = config('admin.media.directory');

        /* The stored name never comes from the client: a UUID avoids collisions,
           overwrites, and anything strange in the original filename. */
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
        $storedName = Str::uuid()->toString().'.'.$extension;

        $path = $file->storeAs($directory, $storedName, ['disk' => $disk]);

        if ($path === false) {
            throw new \RuntimeException('The file could not be written to storage.');
        }

        [$width, $height] = $this->dimensions($disk, $path);

        try {
            return Media::create([
                'disk' => $disk,
                'path' => $path,
                'filename' => $this->safeOriginalName($file),
                'mime_type' => $file->getClientMimeType(),
                'extension' => $extension,
                'size_bytes' => $file->getSize(),
                'width' => $width,
                'height' => $height,
                'created_by' => $userId,
            ]);
        } catch (Throwable $e) {
            /* Never leave a file behind that no row points at. */
            Storage::disk($disk)->delete($path);

            throw $e;
        }
    }

    /**
     * Removes the row and the file together. A file that has already vanished
     * is not an error — the row still needs clearing.
     */
    public function delete(Media $media): void
    {
        $disk = Storage::disk($media->disk);

        if ($disk->exists($media->path)) {
            $disk->delete($media->path);
        }

        $media->delete();
    }

    /**
     * Width and height are useful but optional: an unreadable file must not turn
     * a successful upload into a failure.
     *
     * @return array{0: int|null, 1: int|null}
     */
    private function dimensions(string $disk, string $path): array
    {
        try {
            $absolute = Storage::disk($disk)->path($path);
            $size = @getimagesize($absolute);

            if (is_array($size)) {
                return [$size[0], $size[1]];
            }
        } catch (Throwable) {
            // Fall through to unknown dimensions.
        }

        return [null, null];
    }

    /**
     * Kept only for display. The client name never reaches the filesystem.
     */
    private function safeOriginalName(UploadedFile $file): string
    {
        $name = $file->getClientOriginalName();

        return Str::limit(basename(str_replace('\\', '/', $name)), 180, '');
    }
}
