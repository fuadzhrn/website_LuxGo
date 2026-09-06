<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqItem;
use App\Models\Page;
use App\Models\PageSection;
use App\Services\PageContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * FAQ entries for a section that declares one. Unlike the fixed sections, the
 * list itself is editable: entries can be added, reordered and switched off.
 * Any page can host a FAQ list — the section definition decides.
 */
class FaqController extends Controller
{
    public function __construct(private readonly PageContentService $content) {}

    public function index(Page $page, PageSection $section): View
    {
        $definition = $this->faqSection($page, $section);

        return view('admin.content.faq.index', [
            'page' => $page,
            'section' => $section,
            'definition' => $definition,
            'items' => $section->faqItems()->with('translations')->get(),
        ]);
    }

    public function create(Page $page, PageSection $section): View
    {
        $definition = $this->faqSection($page, $section);

        return view('admin.content.faq.form', [
            'page' => $page,
            'section' => $section,
            'definition' => $definition,
            'item' => new FaqItem(['is_active' => true, 'shows_usage_breakdown' => false]),
        ]);
    }

    public function store(Request $request, Page $page, PageSection $section): RedirectResponse
    {
        $this->faqSection($page, $section);
        $data = $this->validated($request);

        DB::transaction(function () use ($section, $data) {
            $item = $section->faqItems()->create([
                'is_active' => (bool) $data['is_active'],
                'shows_usage_breakdown' => (bool) ($data['shows_usage_breakdown'] ?? false),
                /* New entries go to the end of the list. */
                'sort_order' => ((int) $section->faqItems()->max('sort_order')) + 1,
            ]);

            $this->saveTranslations($item, $data);
        });

        return redirect()
            ->route('admin.content.faq', [$page, $section])
            ->with('success', 'Changes saved successfully.');
    }

    public function edit(Page $page, PageSection $section, FaqItem $faqItem): View
    {
        $definition = $this->faqSection($page, $section);
        $this->assertBelongs($section, $faqItem);

        return view('admin.content.faq.form', [
            'page' => $page,
            'section' => $section,
            'definition' => $definition,
            'item' => $faqItem,
        ]);
    }

    public function update(Request $request, Page $page, PageSection $section, FaqItem $faqItem): RedirectResponse
    {
        $this->faqSection($page, $section);
        $this->assertBelongs($section, $faqItem);

        $data = $this->validated($request);

        DB::transaction(function () use ($faqItem, $data) {
            $faqItem->update([
                'is_active' => (bool) $data['is_active'],
                'shows_usage_breakdown' => (bool) ($data['shows_usage_breakdown'] ?? false),
            ]);

            $this->saveTranslations($faqItem, $data);
        });

        return redirect()
            ->route('admin.content.faq', [$page, $section])
            ->with('success', 'Changes saved successfully.');
    }

    public function destroy(Page $page, PageSection $section, FaqItem $faqItem): RedirectResponse
    {
        $this->faqSection($page, $section);
        $this->assertBelongs($section, $faqItem);

        $faqItem->delete();

        return redirect()
            ->route('admin.content.faq', [$page, $section])
            ->with('success', 'FAQ deleted.');
    }

    /**
     * Moves one entry up or down by swapping its place with its neighbour, so
     * the order is a plain number the admin never has to type.
     */
    public function move(Request $request, Page $page, PageSection $section, FaqItem $faqItem): RedirectResponse
    {
        $this->faqSection($page, $section);
        $this->assertBelongs($section, $faqItem);

        $direction = $request->input('direction') === 'up' ? 'up' : 'down';
        $items = $section->faqItems()->get();
        $index = $items->search(fn (FaqItem $item) => $item->is($faqItem));
        $swapWith = $items->get($direction === 'up' ? $index - 1 : $index + 1);

        if ($index === false || $swapWith === null) {
            return redirect()->route('admin.content.faq', [$page, $section]);
        }

        DB::transaction(function () use ($items, $index, $direction) {
            /* Rewritten from the current order, so entries that share a
               sort_order cannot get stuck. */
            $reordered = $items->values()->all();
            $target = $direction === 'up' ? $index - 1 : $index + 1;
            [$reordered[$index], $reordered[$target]] = [$reordered[$target], $reordered[$index]];

            foreach ($reordered as $position => $item) {
                $item->update(['sort_order' => $position + 1]);
            }
        });

        return redirect()->route('admin.content.faq', [$page, $section]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $rules = [
            'is_active' => ['required', 'boolean'],
            'shows_usage_breakdown' => ['nullable', 'boolean'],
        ];

        foreach (config('locales.supported') as $locale) {
            $rules["question.{$locale}"] = ['required', 'string', 'max:200'];
            $rules["answer.{$locale}"] = ['required', 'string', 'max:1500'];
        }

        return $request->validate($rules);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function saveTranslations(FaqItem $item, array $data): void
    {
        foreach (config('locales.supported') as $locale) {
            $item->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'question' => $data['question'][$locale],
                    'answer' => $data['answer'][$locale],
                ]
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function faqSection(Page $page, PageSection $section): array
    {
        abort_unless($this->content->isEditable($page->key), 404);

        $definition = $this->content->sectionDefinition($page->key, $section->section_key);

        /* Only a section that declares a FAQ list has these screens. */
        abort_unless($definition !== null && ($definition['faq'] ?? false), 404);
        abort_unless($section->page_id === $page->id, 404);

        return $definition;
    }

    private function assertBelongs(PageSection $section, FaqItem $faqItem): void
    {
        abort_unless($faqItem->page_section_id === $section->id, 404);
    }
}
