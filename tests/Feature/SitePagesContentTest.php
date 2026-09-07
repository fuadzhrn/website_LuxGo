<?php

namespace Tests\Feature;

use App\Models\MembershipSetting;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\PageContentService;
use Database\Seeders\MembershipSettingsSeeder;
use Database\Seeders\PageContentSeeder;
use Database\Seeders\PagesSeeder;
use Database\Seeders\SiteSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The three pages that joined the CMS last: The Experience, How It Works and
 * About & Contact.
 */
class SitePagesContentTest extends TestCase
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
    }

    private function administrator(): User
    {
        return User::factory()->administrator()->create();
    }

    private function page(string $key): Page
    {
        return Page::where('key', $key)->sole();
    }

    private function section(string $pageKey, string $sectionKey): PageSection
    {
        return $this->page($pageKey)->sections()->where('section_key', $sectionKey)->sole();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(string $pageKey, string $sectionKey, array $overrides = []): array
    {
        $definition = config("page_content.pages.{$pageKey}.sections.{$sectionKey}");
        $service = app(PageContentService::class);
        $data = ['is_active' => 1];

        foreach (config('locales.supported') as $locale) {
            $content = $service->editableContent($pageKey, $sectionKey, $locale);

            foreach (array_keys($definition['fields']) as $path) {
                data_set($data, "content.{$locale}.{$path}", data_get($content, $path));
            }
        }

        foreach ($definition['settings'] ?? [] as $key => $setting) {
            $data['settings'][$key] = $setting['default'] ?? null;
        }

        foreach (array_keys($definition['media'] ?? []) as $slot) {
            $data["media_{$slot}_media_id"] = $this->sectionMediaId($pageKey, $sectionKey, $slot);
        }

        return array_replace_recursive($data, $overrides);
    }

    private function sectionMediaId(string $pageKey, string $sectionKey, string $slot): ?int
    {
        return $this->section($pageKey, $sectionKey)->mediaForSlot($slot)?->id;
    }

    /* The Experience ------------------------------------------------------- */

    public function test_the_experience_copy_moved_into_the_database(): void
    {
        $hero = $this->section('experience', 'hero');
        $this->assertSame('Pengalaman', $hero->translation('id')->content['eyebrow']);
        $this->assertSame('The Experience', $hero->translation('en')->content['eyebrow']);

        $driver = $this->section('experience', 'not_just_driver');
        $this->assertSame('Penampilan Profesional', $driver->translation('id')->content['attributes']['appearance']);
        $this->assertSame('Professional Appearance', $driver->translation('en')->content['attributes']['appearance']);
    }

    public function test_the_experience_keeps_exactly_seven_attributes_and_three_pillars(): void
    {
        $driverFields = config('page_content.pages.experience.sections.not_just_driver.fields');
        $attributes = array_filter(array_keys($driverFields), fn (string $key) => str_starts_with($key, 'attributes.'));
        $this->assertCount(7, $attributes);

        $standardFields = config('page_content.pages.experience.sections.service_standard.fields');
        $pillars = array_filter(array_keys($standardFields), fn (string $key) => str_starts_with($key, 'pillars.'));
        /* Three pillars, each a title and a copy line. */
        $this->assertCount(6, $pillars);

        $response = $this->get(route('experience', ['locale' => 'id']))->assertOk();
        $this->assertSame(7, substr_count($response->getContent(), 'experience-driver__item'));
        $this->assertSame(3, substr_count($response->getContent(), 'class="experience-standard__pillar"'));
    }

    public function test_the_experience_reads_the_database_in_both_locales(): void
    {
        $this->get(route('experience', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('Penampilan Profesional')
            ->assertSee('Privasi & Kerahasiaan');

        $this->get(route('experience', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('Professional Appearance')
            ->assertSee('Privacy &amp; Confidentiality', false);
    }

    public function test_an_experience_edit_reaches_the_page(): void
    {
        $section = $this->section('experience', 'not_just_driver');

        $this->actingAs($this->administrator())
            ->put(route('admin.content.section.update', [$this->page('experience'), $section]), $this->payload('experience', 'not_just_driver', [
                'content' => [
                    'id' => ['attributes' => ['punctual' => 'Selalu Tepat Waktu']],
                    'en' => ['attributes' => ['punctual' => 'Always Punctual']],
                ],
            ]))
            ->assertSessionHas('success', 'Changes saved successfully.');

        $this->get(route('experience', ['locale' => 'id']))->assertOk()->assertSee('Selalu Tepat Waktu');
        $this->get(route('experience', ['locale' => 'en']))->assertOk()->assertSee('Always Punctual');
    }

    public function test_an_inactive_experience_section_is_hidden_and_returns(): void
    {
        $section = $this->section('experience', 'not_just_driver');

        $section->update(['is_active' => false]);
        $this->get(route('experience', ['locale' => 'id']))->assertOk()->assertDontSee('experience-driver__list', false);

        $section->update(['is_active' => true]);
        $this->get(route('experience', ['locale' => 'id']))->assertOk()->assertSee('experience-driver__list', false);
    }

    public function test_a_missing_experience_line_falls_back_to_english(): void
    {
        $section = $this->section('experience', 'hero');
        $indonesian = $section->translation('id');
        $content = $indonesian->content;
        unset($content['copy']);
        $indonesian->content = $content;
        $indonesian->save();

        $this->get(route('experience', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('Premium mobility shaped by thoughtful service, professional driving, and attention to every journey.');
    }

    /* How It Works --------------------------------------------------------- */

    public function test_how_it_works_keeps_exactly_three_steps(): void
    {
        $fields = config('page_content.pages.how_it_works.sections.process.fields');
        $steps = array_filter(array_keys($fields), fn (string $key) => str_starts_with($key, 'steps.'));
        /* Three steps, each a title and a copy line. */
        $this->assertCount(6, $steps);

        $response = $this->get(route('how-it-works', ['locale' => 'id']))->assertOk();
        $this->assertSame(3, substr_count($response->getContent(), 'class="hiw-process__step"'));
        $response->assertSee('Join')->assertSee('Book')->assertSee('Use');
    }

    public function test_the_usage_duration_comes_from_the_membership_settings(): void
    {
        $this->get(route('how-it-works', ['locale' => 'id']))->assertOk()->assertSee('selama 12 jam');

        MembershipSetting::query()->first()->update(['usage_duration_hours' => 10]);

        $this->get(route('how-it-works', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('selama 10 jam')
            ->assertDontSee('selama 12 jam');

        $this->get(route('how-it-works', ['locale' => 'en']))->assertOk()->assertSee('for 10 hours');

        /* The figure is never stored in the copy itself. */
        $stored = $this->section('how_it_works', 'process')->translation('id')->content['steps']['use']['copy'];
        $this->assertStringContainsString('{{usage_duration}}', $stored);
    }

    public function test_the_service_area_is_the_published_one(): void
    {
        $response = $this->get(route('how-it-works', ['locale' => 'id']))->assertOk();

        foreach (['Jakarta', 'Tangerang', 'Bekasi', 'Bogor', 'Depok'] as $region) {
            $response->assertSee($region);
        }

        $response->assertSee('Pusat · Utara · Selatan · Barat · Timur');
        $response->assertSee('Kota Tangerang · Tangerang Selatan · Kabupaten Tangerang');
        $response->assertSee('Kota Bekasi · Kabupaten Bekasi');
        $response->assertSee('Kota Bogor · Kabupaten Bogor');
        $response->assertSee('Kota Depok');

        /* Five rows, no more: the area is not something the editor adds to. */
        $this->assertSame(5, substr_count($response->getContent(), 'class="hiw-area__row"'));
    }

    public function test_the_hero_index_uses_the_step_titles_rather_than_its_own(): void
    {
        $process = $this->section('how_it_works', 'process');
        $translation = $process->translation('id');
        $content = $translation->content;
        $content['steps']['join']['title'] = 'Gabung';
        $translation->content = $content;
        $translation->save();

        $this->get(route('how-it-works', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('hiw-hero__index-label', false)
            ->assertSee('Gabung');
    }

    public function test_the_how_it_works_cta_keeps_the_locale(): void
    {
        $this->get(route('how-it-works', ['locale' => 'id']))->assertOk()->assertSee('/id/membership');
        $this->get(route('how-it-works', ['locale' => 'en']))->assertOk()->assertSee('/en/membership');
    }

    public function test_an_inactive_how_it_works_section_is_hidden(): void
    {
        $section = $this->section('how_it_works', 'service_area');

        $section->update(['is_active' => false]);
        $this->get(route('how-it-works', ['locale' => 'id']))->assertOk()->assertDontSee('hiw-area__list', false);

        $section->update(['is_active' => true]);
        $this->get(route('how-it-works', ['locale' => 'id']))->assertOk()->assertSee('hiw-area__list', false);
    }

    /* About & Contact ------------------------------------------------------ */

    public function test_about_keeps_exactly_five_audiences(): void
    {
        $fields = config('page_content.pages.about.sections.about.fields');
        $audiences = array_filter(array_keys($fields), fn (string $key) => str_starts_with($key, 'audiences.'));
        $this->assertCount(5, $audiences);

        $response = $this->get(route('about', ['locale' => 'id']))->assertOk();
        $this->assertSame(5, substr_count($response->getContent(), 'class="about-intro__item"'));
        $response->assertSee('Pemilik Bisnis')->assertSee('Korporasi');

        $this->get(route('about', ['locale' => 'en']))->assertOk()->assertSee('Business Owners')->assertSee('Corporate');
    }

    public function test_the_application_form_labels_are_bilingual(): void
    {
        $this->get(route('about', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('Nama Lengkap')
            ->assertSee('Kirim Pengajuan Membership')
            ->assertSee('Mohon isi nama lengkap Anda.', false);

        $this->get(route('about', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('Full Name')
            ->assertSee('Submit Membership Application');
    }

    public function test_an_application_label_edit_reaches_the_page(): void
    {
        $section = $this->section('about', 'membership_application');

        $this->actingAs($this->administrator())
            ->put(route('admin.content.section.update', [$this->page('about'), $section]), $this->payload('about', 'membership_application', [
                'content' => ['id' => ['field_name' => 'Nama Anda'], 'en' => ['field_name' => 'Your Name']],
            ]))
            ->assertSessionHasNoErrors();

        $this->get(route('about', ['locale' => 'id']))->assertOk()->assertSee('Nama Anda');
        $this->get(route('about', ['locale' => 'en']))->assertOk()->assertSee('Your Name');
    }

    public function test_the_contact_labels_are_bilingual_and_the_values_come_from_the_settings(): void
    {
        $this->get(route('about', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('Mari Bicara')
            ->assertSee('info@luxandgo.com')
            ->assertSee('PT Dwimuria Investama Properti')
            ->assertSee('Gajah Mada Tower, Lt. 19-01');

        $this->get(route('about', ['locale' => 'en']))->assertOk()->assertSee('Let’s Talk');

        /* The values live in the settings, never in the page content. */
        $content = $this->section('about', 'contact')->translation('id')->content;
        $flat = json_encode($content);
        $this->assertStringNotContainsString('info@luxandgo.com', $flat);
        $this->assertStringNotContainsString('0811', $flat);
        $this->assertStringNotContainsString('Dwimuria', $flat);
    }

    public function test_the_contact_links_are_built_from_the_stored_details(): void
    {
        $response = $this->get(route('about', ['locale' => 'id']))->assertOk();

        $response->assertSee('tel:+6281112341234', false);
        $response->assertSee('mailto:info@luxandgo.com', false);
        $response->assertSee('https://www.instagram.com/luxandgo', false);
        $response->assertSee('https://www.tiktok.com/@luxandgo', false);
    }

    public function test_a_channel_without_a_stored_value_is_not_rendered(): void
    {
        SiteSetting::where('key', 'tiktok_handle')->update(['value' => '']);

        $this->get(route('about', ['locale' => 'id']))
            ->assertOk()
            ->assertDontSee('https://www.tiktok.com', false);
    }

    public function test_an_inactive_about_section_is_hidden(): void
    {
        $section = $this->section('about', 'contact');

        $section->update(['is_active' => false]);
        $this->get(route('about', ['locale' => 'id']))->assertOk()->assertDontSee('about-contact__list', false);

        $section->update(['is_active' => true]);
        $this->get(route('about', ['locale' => 'id']))->assertOk()->assertSee('about-contact__list', false);
    }

    /* Global settings ------------------------------------------------------ */

    public function test_one_settings_change_reaches_every_page_that_shows_it(): void
    {
        SiteSetting::where('key', 'email')->update(['value' => 'halo@luxandgo.com']);
        SiteSetting::where('key', 'company_name')->update(['value' => 'PT Contoh Baru']);

        /* The contact section, the footer on every page, and the legal pages. */
        $this->get(route('about', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('halo@luxandgo.com')
            ->assertSee('PT Contoh Baru')
            ->assertDontSee('info@luxandgo.com');

        $this->get(route('home', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('halo@luxandgo.com')
            ->assertDontSee('info@luxandgo.com');

        $this->get(route('legal.terms', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('halo@luxandgo.com')
            ->assertDontSee('info@luxandgo.com');
    }

    /* Editor --------------------------------------------------------------- */

    public function test_every_page_now_has_an_editor_with_its_sections(): void
    {
        $admin = $this->administrator();

        $expected = [
            'experience' => ['Experience Hero', 'Not Just a Driver', 'Service Standard'],
            'how_it_works' => ['How It Works Hero', 'Join / Book / Use', 'Serving Jabodetabek', 'Closing CTA'],
            'about' => ['About LUX&amp;GO', 'Membership Application', 'Contact &amp; Head Office'],
        ];

        foreach ($expected as $pageKey => $labels) {
            $response = $this->actingAs($admin)
                ->get(route('admin.content.page', $this->page($pageKey)))
                ->assertOk();

            foreach ($labels as $label) {
                $response->assertSee($label, false);
            }
        }
    }

    public function test_the_experience_editor_shows_both_languages_and_the_image_field(): void
    {
        $this->actingAs($this->administrator())
            ->get(route('admin.content.section.edit', [$this->page('experience'), $this->section('experience', 'not_just_driver')]))
            ->assertOk()
            ->assertSee('name="content[id][attributes][appearance]"', false)
            ->assertSee('name="content[en][attributes][appearance]"', false)
            ->assertSee('name="media_driver_image"', false)
            ->assertSee('Bahasa Indonesia')
            ->assertSee('English');
    }

    public function test_every_become_a_member_link_reaches_the_application_form(): void
    {
        foreach (['id', 'en'] as $locale) {
            $expected = route('about', ['locale' => $locale]).'#membership-application';

            /* The navbar and footer are on every page; the membership and
               experience CTAs point at the same destination. */
            foreach (['home', 'membership', 'experience', 'how-it-works'] as $route) {
                $this->get(route($route, ['locale' => $locale]))
                    ->assertOk()
                    ->assertSee($expected, false)
                    ->assertDontSee('href="/become-a-member"', false);
            }
        }
    }

    public function test_a_guest_and_a_non_admin_cannot_edit_these_pages(): void
    {
        $url = route('admin.content.section.edit', [$this->page('about'), $this->section('about', 'contact')]);

        $this->get($url)->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->get($url)->assertForbidden();
    }
}
