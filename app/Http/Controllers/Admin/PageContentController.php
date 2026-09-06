<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePageSectionRequest;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Services\PageContentService;
use App\Services\PageSectionWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Throwable;

/**
 * One controller for every CMS page. What a page contains comes from
 * config/page_content.php, so Membership and the rest need no controller of
 * their own — only their entry in that file.
 */
class PageContentController extends Controller
{
    public function __construct(
        private readonly PageContentService $content,
        private readonly PageSectionWriter $writer,
    ) {}

    public function index(): View
    {
        return view('admin.content.index', [
            'pages' => Page::withCount('sections')->orderBy('sort_order')->get(),
        ]);
    }

    /**
     * The sections of one page, each with its status and an edit action.
     */
    public function show(Page $page): View
    {
        $definition = $this->editableDefinition($page);

        return view('admin.content.page', [
            'page' => $page,
            'definition' => $definition,
            'sections' => $this->orderedSections($page, $definition),
        ]);
    }

    public function edit(Page $page, PageSection $section): View
    {
        $definition = $this->sectionDefinition($page, $section);

        $content = [];

        foreach (config('locales.supported') as $locale) {
            $content[$locale] = $this->content->editableContent($page->key, $section->section_key, $locale);
        }

        return view('admin.content.section', [
            'page' => $page,
            'section' => $section,
            'definition' => $definition,
            'content' => $content,
            'media' => $this->slotMedia($section, $definition),
        ]);
    }

    public function update(UpdatePageSectionRequest $request, Page $page, PageSection $section): RedirectResponse
    {
        $definition = $this->sectionDefinition($page, $section);

        $files = [];

        foreach (array_keys(Arr::get($definition, 'media', [])) as $slot) {
            $files[$slot] = $request->file("media_{$slot}");
        }

        try {
            $this->writer->save($section, $definition, $request->validated(), $files, $request->user()->id);
        } catch (Throwable) {
            return back()->withInput()->with('error', 'Unable to save this section. No changes were made.');
        }

        return redirect()
            ->route('admin.content.section.edit', [$page, $section])
            ->with('success', 'Changes saved successfully.');
    }

    /**
     * @param  array<string, mixed>  $definition
     * @return array<int, array{model: PageSection, key: string, label: string}>
     */
    private function orderedSections(Page $page, array $definition): array
    {
        $sections = [];

        /* Ordered by the definition, which is the order the page renders in. */
        foreach (Arr::get($definition, 'sections', []) as $key => $sectionDefinition) {
            $model = $page->sections->firstWhere('section_key', $key);

            if ($model) {
                $sections[] = ['model' => $model, 'key' => $key, 'label' => $sectionDefinition['label']];
            }
        }

        return $sections;
    }

    /**
     * @param  array<string, mixed>  $definition
     * @return array<string, Media|null>
     */
    private function slotMedia(PageSection $section, array $definition): array
    {
        $media = [];

        foreach (array_keys(Arr::get($definition, 'media', [])) as $slot) {
            $media[$slot] = $section->mediaForSlot($slot);
        }

        return $media;
    }

    /**
     * @return array<string, mixed>
     */
    private function editableDefinition(Page $page): array
    {
        /* A page key that has no editor yet is not a 404 waiting to happen in
           the sidebar — it simply cannot be opened. */
        abort_unless($this->content->isEditable($page->key), 404);

        return $this->content->definition($page->key) ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    private function sectionDefinition(Page $page, PageSection $section): array
    {
        $this->editableDefinition($page);

        $definition = $this->content->sectionDefinition($page->key, $section->section_key);

        /* Guards against a section key that exists in the database but not in
           the definition — the editor only ever writes fields it knows. */
        abort_if($definition === null, 404);

        return $definition;
    }
}
