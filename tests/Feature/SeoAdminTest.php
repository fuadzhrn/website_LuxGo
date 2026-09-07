<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Page;
use App\Models\SeoSetting;
use App\Models\SeoTranslation;
use App\Models\User;
use Database\Seeders\MembershipSettingsSeeder;
use Database\Seeders\PageContentSeeder;
use Database\Seeders\PagesSeeder;
use Database\Seeders\SiteSettingsSeeder;
use Database\Seeders\VehicleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeoAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed(PagesSeeder::class);
        $this->seed(MembershipSettingsSeeder::class);
        $this->seed(SiteSettingsSeeder::class);
        $this->seed(PageContentSeeder::class);
        $this->seed(VehicleSeeder::class);
    }

    private function administrator(): User
    {
        return User::factory()->administrator()->create();
    }

    private function page(string $key): Page
    {
        return Page::where('key', $key)->sole();
    }

    private function setting(string $key): SeoSetting
    {
        return $this->page($key)->seoSetting->fresh(['translations']);
    }

    /**
     * The editor posts every field it renders. This mirrors that.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(string $pageKey, array $overrides = []): array
    {
        $setting = $this->setting($pageKey);
        $data = [
            'is_indexable' => (int) $setting->is_indexable,
            'media_og_image_media_id' => $setting->og_media_id,
        ];

        foreach (config('locales.supported') as $locale) {
            $translation = $setting->translations->firstWhere('locale', $locale);

            foreach (['meta_title', 'meta_description', 'og_title', 'og_description'] as $field) {
                $data['seo'][$locale][$field] = $translation?->{$field};
            }
        }

        return array_replace_recursive($data, $overrides);
    }

    /* Initial data --------------------------------------------------------- */

    public function test_each_page_starts_from_the_meta_lines_it_shipped_with(): void
    {
        $membership = $this->setting('membership');

        $this->assertSame('Membership — LUX&GO', $membership->translations->firstWhere('locale', 'id')->meta_title);
        $this->assertSame(
            'Koleksi Kami — LUX&GO',
            $this->setting('collection')->translations->firstWhere('locale', 'id')->meta_title
        );
        $this->assertSame(
            'Our Collection — LUX&GO',
            $this->setting('collection')->translations->firstWhere('locale', 'en')->meta_title
        );
    }

    public function test_seeding_again_creates_no_second_record_or_translation(): void
    {
        $this->seed(PageContentSeeder::class);

        $this->assertSame(6, SeoSetting::count());
        $this->assertSame(12, SeoTranslation::count());
        $this->assertSame(1, $this->page('home')->seoSetting()->count());
    }

    /* List ----------------------------------------------------------------- */

    public function test_the_list_shows_exactly_the_six_pages(): void
    {
        $response = $this->actingAs($this->administrator())
            ->get(route('admin.seo'))
            ->assertOk();

        foreach (['Home', 'Membership', 'Our Collection', 'The Experience', 'How It Works', 'About &amp; Contact'] as $label) {
            $response->assertSee($label, false);
        }

        $this->assertSame(6, substr_count($response->getContent(), 'Edit SEO'));
    }

    public function test_the_list_reports_the_real_state_of_each_locale(): void
    {
        $response = $this->actingAs($this->administrator())->get(route('admin.seo'))->assertOk();
        $response->assertSee('Complete');
        $response->assertSee('Indexable');

        /* Emptying a locale is reported, not assumed. */
        $setting = $this->setting('about');
        $setting->translations()->where('locale', 'en')->update(['meta_title' => null, 'meta_description' => null]);
        $setting->translations()->where('locale', 'id')->update(['meta_title' => null, 'meta_description' => null]);
        $setting->update(['is_indexable' => false]);

        $this->actingAs($this->administrator())
            ->get(route('admin.seo'))
            ->assertOk()
            ->assertSee('Missing')
            ->assertSee('Not indexable');
    }

    /* Edit ----------------------------------------------------------------- */

    public function test_the_editor_offers_both_languages_and_the_shared_settings(): void
    {
        $this->actingAs($this->administrator())
            ->get(route('admin.seo.edit', $this->page('membership')))
            ->assertOk()
            ->assertSee('name="seo[id][meta_title]"', false)
            ->assertSee('name="seo[en][meta_title]"', false)
            ->assertSee('name="seo[id][og_description]"', false)
            ->assertSee('name="media_og_image"', false)
            ->assertSee('name="is_indexable"', false)
            ->assertSee('Bahasa Indonesia')
            ->assertSee('English');
    }

    public function test_saving_stores_both_languages_without_duplicating_rows(): void
    {
        $page = $this->page('membership');

        $this->actingAs($this->administrator())
            ->put(route('admin.seo.update', $page), $this->payload('membership', [
                'seo' => [
                    'id' => ['meta_title' => 'Membership Indonesia', 'meta_description' => 'Deskripsi Indonesia.'],
                    'en' => ['meta_title' => 'Membership English', 'og_title' => 'Share English'],
                ],
            ]))
            ->assertRedirect(route('admin.seo.edit', $page))
            ->assertSessionHas('success', 'SEO settings updated successfully.');

        $setting = $this->setting('membership');
        $this->assertSame(2, $setting->translations->count());
        $this->assertSame('Membership Indonesia', $setting->translations->firstWhere('locale', 'id')->meta_title);
        $this->assertSame('Membership English', $setting->translations->firstWhere('locale', 'en')->meta_title);
        $this->assertSame('Share English', $setting->translations->firstWhere('locale', 'en')->og_title);
    }

    public function test_the_editor_never_repoints_a_record_at_another_page(): void
    {
        $page = $this->page('membership');
        $home = $this->page('home');

        $this->actingAs($this->administrator())->put(
            route('admin.seo.update', $page),
            $this->payload('membership', ['page_id' => $home->id])
        );

        $this->assertSame($page->id, $this->setting('membership')->page_id);
        $this->assertSame($home->id, $this->setting('home')->page_id);
    }

    public function test_overlong_text_is_rejected(): void
    {
        $page = $this->page('membership');

        $this->actingAs($this->administrator())
            ->put(route('admin.seo.update', $page), $this->payload('membership', [
                'seo' => ['id' => ['meta_title' => str_repeat('a', 200)]],
            ]))
            ->assertSessionHasErrors('seo.id.meta_title');
    }

    /* Public output -------------------------------------------------------- */

    public function test_the_public_head_uses_the_record_for_its_locale(): void
    {
        $this->actingAs($this->administrator())->put(
            route('admin.seo.update', $this->page('membership')),
            $this->payload('membership', [
                'seo' => [
                    'id' => ['meta_title' => 'Judul Indonesia', 'meta_description' => 'Deskripsi Indonesia.'],
                    'en' => ['meta_title' => 'English Title', 'meta_description' => 'English description.'],
                ],
            ])
        );

        $this->get(route('membership', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('<title>Judul Indonesia</title>', false)
            ->assertSee('content="Deskripsi Indonesia."', false)
            ->assertDontSee('English Title', false);

        $this->get(route('membership', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('<title>English Title</title>', false)
            ->assertSee('content="English description."', false);
    }

    public function test_a_missing_indonesian_value_falls_back_to_english(): void
    {
        $setting = $this->setting('experience');
        $setting->translations()->where('locale', 'en')->update(['meta_title' => 'English Only Title']);
        $setting->translations()->where('locale', 'id')->update(['meta_title' => null]);

        $this->get(route('experience', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('<title>English Only Title</title>', false);
    }

    public function test_the_share_fields_fall_back_to_the_search_fields(): void
    {
        $this->actingAs($this->administrator())->put(
            route('admin.seo.update', $this->page('about')),
            $this->payload('about', [
                'seo' => ['id' => ['meta_title' => 'Judul Halaman', 'meta_description' => 'Deskripsi halaman.', 'og_title' => null, 'og_description' => null]],
            ])
        );

        $this->get(route('about', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('property="og:title" content="Judul Halaman"', false)
            ->assertSee('property="og:description" content="Deskripsi halaman."', false);
    }

    public function test_the_canonical_and_alternates_follow_the_locale(): void
    {
        foreach (['id', 'en'] as $locale) {
            $this->get(route('membership', ['locale' => $locale]))
                ->assertOk()
                ->assertSee('rel="canonical" href="'.route('membership', ['locale' => $locale]).'"', false)
                ->assertSee('hreflang="id" href="'.route('membership', ['locale' => 'id']).'"', false)
                ->assertSee('hreflang="en" href="'.route('membership', ['locale' => 'en']).'"', false)
                ->assertSee('hreflang="x-default"', false)
                ->assertSee('<html lang="'.$locale.'">', false);
        }
    }

    public function test_the_indexable_toggle_changes_the_robots_directive(): void
    {
        $page = $this->page('collection');

        $this->get(route('collection', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('name="robots" content="index, follow"', false);

        $this->actingAs($this->administrator())
            ->put(route('admin.seo.update', $page), $this->payload('collection', ['is_indexable' => 0]))
            ->assertSessionHasNoErrors();

        $this->get(route('collection', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('name="robots" content="noindex, nofollow"', false);

        $this->actingAs($this->administrator())
            ->put(route('admin.seo.update', $page), $this->payload('collection', ['is_indexable' => 1]));

        $this->get(route('collection', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('name="robots" content="index, follow"', false);
    }

    public function test_every_page_renders_a_title_and_description_in_both_locales(): void
    {
        foreach (['home', 'membership', 'collection', 'experience', 'how-it-works', 'about'] as $route) {
            foreach (['id', 'en'] as $locale) {
                $body = $this->get(route($route, ['locale' => $locale]))->assertOk()->getContent();

                $this->assertMatchesRegularExpression('/<title>[^<]{5,}<\/title>/', $body, "{$route} {$locale}");
                $this->assertMatchesRegularExpression('/name="description" content="[^"]{5,}"/', $body, "{$route} {$locale}");
                $this->assertStringNotContainsString('<title></title>', $body);
            }
        }
    }

    /* OG image ------------------------------------------------------------- */

    public function test_an_uploaded_og_image_is_shared_by_both_locales_and_protected_from_deletion(): void
    {
        $page = $this->page('home');

        $this->actingAs($this->administrator())
            ->put(route('admin.seo.update', $page), $this->payload('home', [
                'media_og_image' => UploadedFile::fake()->image('share.jpg', 1200, 630),
            ]))
            ->assertSessionHasNoErrors();

        $media = $this->setting('home')->fresh()->ogMedia;
        $this->assertNotNull($media);
        Storage::disk('public')->assertExists($media->path);

        foreach (['id', 'en'] as $locale) {
            $this->get(route('home', ['locale' => $locale]))
                ->assertOk()
                ->assertSee('property="og:image" content="'.$media->url().'"', false);
        }

        /* The library refuses to delete an image a page still shares. */
        $this->assertTrue($media->isInUse());
        $this->assertContains('seo_settings', $media->usedBy());

        $this->actingAs($this->administrator())
            ->delete(route('admin.media.destroy', $media))
            ->assertSessionHas('error', 'This media is currently in use and cannot be deleted.');

        $this->assertNotNull($media->fresh());
    }

    public function test_an_existing_library_image_can_be_chosen_and_then_released(): void
    {
        $page = $this->page('home');
        $existing = Media::query()->firstOrFail();

        $this->actingAs($this->administrator())
            ->put(route('admin.seo.update', $page), $this->payload('home', ['media_og_image_media_id' => $existing->id]))
            ->assertSessionHasNoErrors();

        $this->assertSame($existing->id, $this->setting('home')->fresh()->og_media_id);

        /* Removing it detaches the relation only. */
        $this->actingAs($this->administrator())
            ->put(route('admin.seo.update', $page), $this->payload('home', ['media_og_image_remove' => 1]));

        $this->assertNull($this->setting('home')->fresh()->og_media_id);
        $this->assertNotNull($existing->fresh());
        Storage::disk('public')->assertExists($existing->path);
    }

    /* Access --------------------------------------------------------------- */

    public function test_a_guest_and_a_non_admin_cannot_reach_or_change_seo(): void
    {
        $page = $this->page('membership');

        $this->get(route('admin.seo'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.seo.edit', $page))->assertRedirect(route('admin.login'));

        $user = User::factory()->create();
        $this->actingAs($user)->get(route('admin.seo'))->assertForbidden();
        $this->actingAs($user)->put(route('admin.seo.update', $page), $this->payload('membership'))->assertForbidden();

        $this->assertSame('Membership — LUX&GO', $this->setting('membership')->translations->firstWhere('locale', 'id')->meta_title);
    }

    public function test_a_page_the_site_does_not_publish_has_no_seo_screen(): void
    {
        $unknown = Page::create(['key' => 'not-published', 'slug' => 'not-published']);

        $this->actingAs($this->administrator())
            ->get(route('admin.seo.edit', $unknown))
            ->assertNotFound();
    }
}
