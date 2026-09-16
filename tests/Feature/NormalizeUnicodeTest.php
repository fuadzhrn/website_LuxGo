<?php

namespace Tests\Feature;

use App\Http\Middleware\NormalizeUnicode;
use App\Models\Page;
use App\Models\SeoSetting;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\MembershipSettingsSeeder;
use Database\Seeders\PageContentSeeder;
use Database\Seeders\PagesSeeder;
use Database\Seeders\SiteSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Covers the repair of text that arrives in the wrong encoding, which is how the
 * home page title came to read "LUX&GO ? Membership Mobilitas Premium".
 */
class NormalizeUnicodeTest extends TestCase
{
    use RefreshDatabase;

    /** The em dash as Word writes it: one byte, meaningless in UTF-8. */
    private const WORD_EM_DASH = "\x97";

    private const UTF8_EM_DASH = "\u{2014}";

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
        return Page::where('key', $key)->firstOrFail();
    }

    private function setting(string $key): SeoSetting
    {
        return SeoSetting::with('translations')->where('page_id', $this->page($key)->id)->firstOrFail();
    }

    /**
     * The SEO form posts every field it renders, so a partial post would blank
     * the rest. This fills them the way the screen does.
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

    private function metaTitle(string $pageKey, string $locale): ?string
    {
        return $this->setting($pageKey)->translations->firstWhere('locale', $locale)?->meta_title;
    }

    /* The original fault ---------------------------------------------------- */

    public function test_a_title_pasted_from_word_is_stored_as_usable_utf8(): void
    {
        $pasted = 'LUX&GO '.self::WORD_EM_DASH.' Membership Mobilitas Premium';
        $this->assertFalse(mb_check_encoding($pasted, 'UTF-8'), 'the fixture must start out broken');

        $this->actingAs($this->administrator())->put(
            route('admin.seo.update', $this->page('home')),
            $this->payload('home', ['seo' => ['id' => ['meta_title' => $pasted]]])
        );

        $stored = $this->metaTitle('home', 'id');

        $this->assertTrue(mb_check_encoding((string) $stored, 'UTF-8'));
        $this->assertSame('LUX&GO '.self::UTF8_EM_DASH.' Membership Mobilitas Premium', $stored);
    }

    public function test_the_repaired_title_reaches_the_page_without_a_replacement_mark(): void
    {
        $this->actingAs($this->administrator())->put(
            route('admin.seo.update', $this->page('home')),
            $this->payload('home', [
                'seo' => ['id' => ['meta_title' => 'LUX&GO '.self::WORD_EM_DASH.' Membership Mobilitas Premium']],
            ])
        );

        $html = $this->get(route('home', ['locale' => 'id']))->assertOk()->getContent();

        $this->assertStringContainsString('<title>LUX&amp;GO '.self::UTF8_EM_DASH.' Membership Mobilitas Premium</title>', $html);
        $this->assertStringNotContainsString("\u{FFFD}", $html);
    }

    /* Everything else is left alone ----------------------------------------- */

    public function test_text_that_is_already_utf8_is_stored_byte_for_byte(): void
    {
        $intact = 'Koleksi Kami '.self::UTF8_EM_DASH.' LUX&GO · "kutipan" — émoji 🚗 aman';

        $this->actingAs($this->administrator())->put(
            route('admin.seo.update', $this->page('home')),
            $this->payload('home', ['seo' => ['id' => ['meta_title' => $intact]]])
        );

        $this->assertSame($intact, $this->metaTitle('home', 'id'));
    }

    public function test_every_windows_punctuation_mark_survives_the_trip(): void
    {
        /* The marks Word substitutes as you type, each a single byte there. */
        $pasted = "\x91curly\x92 \x93quotes\x94 \x85 \x96 \x97 \x95 \xA0trailing";

        $this->actingAs($this->administrator())->put(
            route('admin.seo.update', $this->page('home')),
            $this->payload('home', ['seo' => ['en' => ['meta_title' => $pasted]]])
        );

        $stored = (string) $this->metaTitle('home', 'en');

        $this->assertTrue(mb_check_encoding($stored, 'UTF-8'));
        $this->assertSame("\u{2018}curly\u{2019} \u{201C}quotes\u{201D} \u{2026} \u{2013} \u{2014} \u{2022} \u{A0}trailing", $stored);
    }

    public function test_a_password_is_never_rewritten(): void
    {
        /* A password is a byte string the user must be able to repeat exactly,
           so it is excluded the same way TrimStrings excludes it. */
        $middleware = new NormalizeUnicode;
        $transform = (new \ReflectionClass($middleware))->getMethod('transform');
        $transform->setAccessible(true);

        $raw = "rahasia\x97123";

        $this->assertSame($raw, $transform->invoke($middleware, 'password', $raw));
        $this->assertSame($raw, $transform->invoke($middleware, 'current_password', $raw));
        $this->assertNotSame($raw, $transform->invoke($middleware, 'meta_title', $raw));
    }

    public function test_it_reaches_the_other_admin_forms_too(): void
    {
        /* The point of a global middleware: a module that never thought about
           encoding is covered anyway. */
        $this->actingAs($this->administrator())->put(route('admin.settings.update'), [
            'company_name' => 'PT Dwimuria '.self::WORD_EM_DASH.' Investama',
            'phone' => '0811-1234-1234',
            'email' => 'info@luxandgo.com',
            'instagram_handle' => '@luxandgo.id',
            'tiktok_handle' => '@luxandgo.id',
            'head_office_address' => "Gajah Mada Tower, Lt. 19-01\nJakarta Pusat 10130",
            'head_office_city' => 'Jakarta Pusat',
        ]);

        $stored = (string) SiteSetting::where('key', 'company_name')->value('value');

        $this->assertTrue(mb_check_encoding($stored, 'UTF-8'));
        $this->assertSame('PT Dwimuria '.self::UTF8_EM_DASH.' Investama', $stored);
    }
}
