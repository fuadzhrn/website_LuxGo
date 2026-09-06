<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;
use App\Services\PageContentService;
use Database\Seeders\PageContentSeeder;
use Database\Seeders\PagesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed(PagesSeeder::class);
        $this->seed(PageContentSeeder::class);
    }

    private function administrator(): User
    {
        return User::factory()->administrator()->create();
    }

    private function home(): Page
    {
        return Page::where('key', 'home')->sole();
    }

    private function section(string $key): PageSection
    {
        return $this->home()->sections()->where('section_key', $key)->sole();
    }

    /**
     * The editor posts every field it renders, so a save always carries the
     * whole section. This mirrors that.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(string $sectionKey, array $overrides = []): array
    {
        $definition = config("page_content.pages.home.sections.{$sectionKey}");
        $service = app(PageContentService::class);

        $data = ['is_active' => 1];

        foreach (config('locales.supported') as $locale) {
            $content = $service->editableContent('home', $sectionKey, $locale);

            foreach (array_keys($definition['fields']) as $path) {
                data_set($data, "content.{$locale}.{$path}", data_get($content, $path));
            }
        }

        foreach ($definition['settings'] ?? [] as $key => $setting) {
            $data['settings'][$key] = $setting['default'] ?? null;
        }

        foreach (array_keys($definition['media'] ?? []) as $slot) {
            $data["media_{$slot}_media_id"] = $this->section($sectionKey)->mediaForSlot($slot)?->id;
        }

        return array_replace_recursive($data, $overrides);
    }

    /* Content migration ---------------------------------------------------- */

    public function test_the_seeder_moves_the_existing_home_copy_into_the_database(): void
    {
        $hero = $this->section('hero');

        $this->assertSame('Mobilitas', $hero->translation('id')->content['title_1']);
        $this->assertSame('Premium', $hero->translation('en')->content['title_1']);

        $steps = $this->section('how_it_works_preview')->translation('en')->content['steps'];
        $this->assertSame(['join', 'book', 'use'], array_keys($steps));
    }

    public function test_the_seeder_does_not_overwrite_an_edit_when_it_runs_again(): void
    {
        $hero = $this->section('hero');
        $translation = $hero->translation('id');
        $translation->content = array_merge($translation->content, ['title_1' => 'Diedit admin']);
        $translation->save();

        $this->seed(PageContentSeeder::class);

        $this->assertSame('Diedit admin', $this->section('hero')->translation('id')->content['title_1']);
        $this->assertSame(1, $this->section('hero')->translations()->where('locale', 'id')->count());
    }

    public function test_the_seeder_imports_the_shipped_images_once(): void
    {
        $before = Media::count();

        $this->assertNotNull($this->section('hero')->mediaForSlot('hero_image'));
        $this->assertNotNull($this->section('premium_mobility')->mediaForSlot('vehicle_image'));

        $this->seed(PageContentSeeder::class);

        $this->assertSame($before, Media::count());
    }

    /* Admin ---------------------------------------------------------------- */

    public function test_the_home_page_lists_its_five_sections(): void
    {
        $response = $this->actingAs($this->administrator())
            ->get(route('admin.content.page', $this->home()))
            ->assertOk();

        foreach (['Hero', 'Access, Not Ownership', 'Business / Family / Life', 'Premium Mobility Preview', 'How It Works Preview'] as $label) {
            $response->assertSee($label);
        }

        $this->assertCount(5, config('page_content.pages.home.sections'));
    }

    public function test_the_section_list_shows_the_active_state(): void
    {
        $this->section('hero')->update(['is_active' => false]);

        $this->actingAs($this->administrator())
            ->get(route('admin.content.page', $this->home()))
            ->assertOk()
            ->assertSee('Inactive');
    }

    public function test_the_editor_loads_both_languages(): void
    {
        $this->actingAs($this->administrator())
            ->get(route('admin.content.section.edit', [$this->home(), $this->section('hero')]))
            ->assertOk()
            ->assertSee('Kepemilikan.')   // Indonesian panel
            ->assertSee('Ownership.')     // English panel is rendered alongside it
            ->assertSee('name="content[id][title_1]"', false)
            ->assertSee('name="content[en][title_1]"', false)
            ->assertSee('Bahasa Indonesia')
            ->assertSee('English');
    }

    public function test_a_page_without_an_editor_cannot_be_opened(): void
    {
        $membership = Page::where('key', 'membership')->sole();

        $this->actingAs($this->administrator())
            ->get(route('admin.content.page', $membership))
            ->assertNotFound();
    }

    public function test_a_section_of_another_page_is_not_reachable_through_home(): void
    {
        $foreign = Page::where('key', 'membership')->sole()->sections()->where('section_key', 'faq_cta')->sole();

        $this->actingAs($this->administrator())
            ->get(route('admin.content.section.edit', [$this->home(), $foreign]))
            ->assertNotFound();
    }

    public function test_saving_stores_both_languages_and_the_shared_settings(): void
    {
        $admin = $this->administrator();

        $response = $this->actingAs($admin)->put(
            route('admin.content.section.update', [$this->home(), $this->section('hero')]),
            $this->payload('hero', [
                'content' => [
                    'id' => ['title_1' => 'Mobilitas Baru', 'cta' => 'Lihat Membership'],
                    'en' => ['title_1' => 'New Mobility'],
                ],
                'settings' => ['cta_route' => 'collection'],
            ])
        );

        $response->assertRedirect(route('admin.content.section.edit', [$this->home(), $this->section('hero')]));
        $response->assertSessionHas('success', 'Changes saved successfully.');

        $hero = $this->section('hero');
        $this->assertSame('Mobilitas Baru', $hero->translation('id')->content['title_1']);
        $this->assertSame('Lihat Membership', $hero->translation('id')->content['cta']);
        $this->assertSame('New Mobility', $hero->translation('en')->content['title_1']);
        $this->assertSame('collection', $hero->settings['cta_route']);
    }

    public function test_validation_rejects_an_empty_heading_and_saves_nothing(): void
    {
        $this->actingAs($this->administrator())
            ->put(
                route('admin.content.section.update', [$this->home(), $this->section('hero')]),
                $this->payload('hero', ['content' => ['id' => ['title_1' => '']]])
            )
            ->assertSessionHasErrors('content.id.title_1');

        $this->assertSame('Mobilitas', $this->section('hero')->translation('id')->content['title_1']);
    }

    public function test_a_cta_destination_outside_the_list_is_rejected(): void
    {
        $this->actingAs($this->administrator())
            ->put(
                route('admin.content.section.update', [$this->home(), $this->section('hero')]),
                $this->payload('hero', ['settings' => ['cta_route' => 'javascript:alert(1)']])
            )
            ->assertSessionHasErrors('settings.cta_route');
    }

    public function test_an_uploaded_image_joins_the_library_and_the_slot(): void
    {
        $admin = $this->administrator();
        $mediaBefore = Media::count();

        $this->actingAs($admin)->put(
            route('admin.content.section.update', [$this->home(), $this->section('business_family_life')]),
            $this->payload('business_family_life', ['media_family_image' => UploadedFile::fake()->image('family.jpg', 800, 600)])
        )->assertSessionHasNoErrors();

        $this->assertSame($mediaBefore + 1, Media::count());

        $media = $this->section('business_family_life')->mediaForSlot('family_image');
        $this->assertNotNull($media);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_an_existing_library_image_can_be_chosen_for_a_slot(): void
    {
        $admin = $this->administrator();
        $existing = Media::first();

        $this->actingAs($admin)->put(
            route('admin.content.section.update', [$this->home(), $this->section('business_family_life')]),
            $this->payload('business_family_life', ['media_life_image_media_id' => $existing->id])
        )->assertSessionHasNoErrors();

        $this->assertSame($existing->id, $this->section('business_family_life')->mediaForSlot('life_image')->id);
    }

    public function test_removing_an_image_detaches_it_but_keeps_the_file(): void
    {
        $admin = $this->administrator();
        $media = $this->section('hero')->mediaForSlot('hero_image');

        $this->actingAs($admin)->put(
            route('admin.content.section.update', [$this->home(), $this->section('hero')]),
            $this->payload('hero', ['media_hero_image_remove' => 1])
        )->assertSessionHasNoErrors();

        $this->assertNull($this->section('hero')->fresh()->mediaForSlot('hero_image'));
        $this->assertNotNull($media->fresh());
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_media_used_by_a_section_cannot_be_deleted_from_the_library(): void
    {
        $media = $this->section('hero')->mediaForSlot('hero_image');

        $this->assertTrue($media->isInUse());
        $this->assertContains('page_section_media', $media->usedBy());

        $this->actingAs($this->administrator())
            ->delete(route('admin.media.destroy', $media))
            ->assertSessionHas('error', 'This media is currently in use and cannot be deleted.');

        $this->assertNotNull($media->fresh());
    }

    public function test_a_guest_and_a_non_admin_cannot_reach_or_save_the_editor(): void
    {
        $url = route('admin.content.section.edit', [$this->home(), $this->section('hero')]);

        $this->get($url)->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->get($url)->assertForbidden();

        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.section.update', [$this->home(), $this->section('hero')]), $this->payload('hero'))
            ->assertForbidden();
    }

    /* Public --------------------------------------------------------------- */

    public function test_the_public_page_reads_the_database_in_both_locales(): void
    {
        $hero = $this->section('hero');

        foreach (['id' => 'Mobilitas', 'en' => 'Premium'] as $locale => $expected) {
            $translation = $hero->translation($locale);
            $translation->content = array_merge($translation->content, ['title_1' => $expected.' CMS']);
            $translation->save();
        }

        $this->get(route('home', ['locale' => 'id']))->assertOk()->assertSee('Mobilitas CMS');
        $this->get(route('home', ['locale' => 'en']))->assertOk()->assertSee('Premium CMS');
    }

    public function test_a_missing_indonesian_line_falls_back_to_english(): void
    {
        $hero = $this->section('hero');

        $english = $hero->translation('en');
        $english->content = array_merge($english->content, ['description' => 'English fallback copy.']);
        $english->save();

        $indonesian = $hero->translation('id');
        $content = $indonesian->content;
        unset($content['description']);
        $indonesian->content = $content;
        $indonesian->save();

        $this->get(route('home', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('English fallback copy.')
            ->assertDontSee('home.hero.description');
    }

    public function test_an_inactive_section_is_hidden_and_comes_back_when_re_activated(): void
    {
        $section = $this->section('how_it_works_preview');

        $section->update(['is_active' => false]);
        $this->get(route('home', ['locale' => 'id']))->assertOk()->assertDontSee('home-how__steps');

        $section->update(['is_active' => true]);
        $this->get(route('home', ['locale' => 'id']))->assertOk()->assertSee('home-how__steps');
    }

    public function test_the_hero_can_be_switched_off_without_breaking_the_page(): void
    {
        $this->section('hero')->update(['is_active' => false]);

        $this->get(route('home', ['locale' => 'id']))
            ->assertOk()
            ->assertDontSee('home-hero__title')
            ->assertSee('home-access__title');
    }

    public function test_a_cta_keeps_the_visitor_locale(): void
    {
        $this->get(route('home', ['locale' => 'id']))->assertOk()->assertSee('/id/membership');
        $this->get(route('home', ['locale' => 'en']))->assertOk()->assertSee('/en/membership');
    }

    public function test_the_page_shows_the_cms_images_and_no_translation_keys(): void
    {
        $response = $this->get(route('home', ['locale' => 'id']))->assertOk();

        $response->assertSee('/storage/luxgo/media/', false);
        $response->assertDontSee('home.hero.');
        $response->assertDontSee('home.use_cases.');
    }
}
