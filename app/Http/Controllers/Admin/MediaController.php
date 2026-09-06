<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class MediaController extends Controller
{
    public function __construct(private readonly MediaService $media) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $media = Media::query()
            ->when($search !== '', fn ($query) => $query->where('filename', 'like', '%'.$search.'%'))
            ->latest()
            ->paginate(config('admin.media.per_page'))
            ->withQueryString();

        $selected = $request->filled('selected')
            ? Media::find($request->query('selected'))
            : null;

        return view('admin.media.index', [
            'media' => $media,
            'search' => $search,
            'selected' => $selected,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /* Validated against the real MIME type as well as the extension, so a
           renamed file cannot slip through. */
        $maxMegabytes = round(config('admin.images.max_kilobytes') / 1024);

        $request->validate([
            'file' => array_merge(['required', 'file'], config('admin.images.rules')),
        ], [
            'file.required' => 'Choose an image to upload.',
            'file.file' => 'The upload did not arrive complete. Please try again.',
            'file.image' => 'That file is not an image.',
            'file.mimetypes' => 'Only JPG, PNG and WebP images can be uploaded.',
            'file.mimes' => 'Only JPG, PNG and WebP images can be uploaded.',
            'file.max' => 'The image is too large. The limit is '.$maxMegabytes.' MB.',
        ]);

        try {
            $media = $this->media->store($request->file('file'), $request->user()->id);
        } catch (Throwable) {
            return back()->with('error', 'Unable to upload the image. Please try again.');
        }

        return redirect()
            ->route('admin.media', ['selected' => $media->id])
            ->with('success', 'Media uploaded successfully.');
    }

    public function update(Request $request, Media $media): RedirectResponse
    {
        $validated = $request->validate([
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        /* Only the description changes; the stored file is untouched. */
        $media->update(['alt_text' => $validated['alt_text'] ?? null]);

        return redirect()
            ->route('admin.media', ['selected' => $media->id])
            ->with('success', 'Alt text updated.');
    }

    public function destroy(Media $media): RedirectResponse
    {
        if ($media->isInUse()) {
            return redirect()
                ->route('admin.media', ['selected' => $media->id])
                ->with('error', 'This media is currently in use and cannot be deleted.');
        }

        try {
            $this->media->delete($media);
        } catch (Throwable) {
            return back()->with('error', 'Unable to delete this media.');
        }

        return redirect()
            ->route('admin.media')
            ->with('success', 'Media deleted.');
    }
}
