<?php

namespace Tests\Feature;

use App\Models\FaqItem;
use App\Models\Page;
use App\Models\SeoSetting;
use App\Models\SiteSetting;
use App\Models\Vehicle;
use Database\Seeders\MembershipSettingsSeeder;
use Database\Seeders\PageContentSeeder;
use Database\Seeders\PagesSeeder;
use Database\Seeders\SiteSettingsSeeder;
use Database\Seeders\VehicleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The crawler-facing surface: what every public page must carry in its head,
 * plus robots.txt, the sitemap and the 404.
 */
class TechnicalSeoTest extends TestCase
{
    use RefreshDatabase;

    /** Every public page, in the locale a visitor lands on. */
    private const PUBLIC_ROUTES = [
        'home', 'membership', 'collection', 'experience', 'how-it-works', 'about',
        'legal.terms', 'legal.privacy', 'legal.cookies',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed(PagesSeeder::class);
        $this->seed(MembershipSettingsSeeder::class);
        $this->seed(SiteSettingsSeeder::class);
        $this->seed(VehicleSeeder::class);
        $this->seed(PageContentSeeder::class);
    }

    /**
     * @return array<int, string>
     */
    private function urls(string $locale = 'id'): array
    {
        return array_map(fn (string $name) => route($name, ['locale' => $locale]), self::PUBLIC_ROUTES);
    }

    private function countTag(string $html, string $pattern): int
    {
        return preg_match_all($pattern, $html);
    }

    /* The head ------------------------------------------------------------- */

    public function test_every_public_page_carries_each_tag_exactly_once(): void
    {
        $tags = [
            'title' => '~<title>.*?</title>~s',
            'description' => '~<meta name="description"~',
            'robots' => '~<meta name="robots"~',
            'canonical' => '~<link rel="canonical"~',
            'og:title' => '~<meta property="og:title"~',
            'og:description' => '~<meta property="og:description"~',
            'og:url' => '~<meta property="og:url"~',
            'og:image' => '~<meta property="og:image" ~',
            'og:site_name' => '~<meta property="og:site_name"~',
            'twitter:card' => '~<meta name="twitter:card"~',
            'json-ld' => '~<script type="application/ld\+json">~',
        ];

        foreach ($this->urls() as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            foreach ($tags as $name => $pattern) {
                $this->assertSame(1, $this->countTag($html, $pattern), "$name on $url");
            }
        }
    }

    public function test_the_canonical_points_at_the_page_itself_and_ignores_query_strings(): void
    {
        foreach ($this->urls() as $url) {
            $this->get($url.'?utm_source=whatsapp&page=2')
                ->assertOk()
                ->assertSee('<link rel="canonical" href="'.$url.'">', false);
        }
    }

    public function test_no_public_page_is_accidentally_hidden_from_search(): void
    {
        foreach (['id', 'en'] as $locale) {
            foreach ($this->urls($locale) as $url) {
                $this->get($url)->assertOk()->assertSee('<meta name="robots" content="index, follow">', false);
            }
        }
    }

    /**
     * Within a language every page must read differently. Across languages the
     * two versions of one page are free to match — hreflang already tells Google
     * they are translations, not duplicates.
     */
    public function test_titles_and_descriptions_are_unique_within_each_language(): void
    {
        foreach (['id', 'en'] as $locale) {
            $titles = [];
            $descriptions = [];

            foreach ($this->urls($locale) as $url) {
                $html = $this->get($url)->assertOk()->getContent();

                preg_match('~<title>(.*?)</title>~s', $html, $title);
                preg_match('~<meta name="description" content="(.*?)">~s', $html, $description);

                $titles[$url] = $title[1];
                $descriptions[$url] = $description[1];
            }

            $this->assertSame(array_unique($titles), $titles, "two $locale pages share a title");
            $this->assertSame(array_unique($descriptions), $descriptions, "two $locale pages share a description");
        }
    }

    public function test_the_legal_pages_are_named_in_the_language_being_read(): void
    {
        $this->get(route('legal.terms', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('<title>Ketentuan Penggunaan — LUX&amp;GO</title>', false);

        $this->get(route('legal.terms', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('<title>Terms of Use — LUX&amp;GO</title>', false);
    }

    public function test_a_page_without_its_own_sharing_image_falls_back_to_one_that_exists(): void
    {
        $fallback = asset('assets/images/luxgo/global/og-default.jpg');

        $this->get(route('home', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('<meta property="og:image" content="'.$fallback.'">', false)
            ->assertSee('<meta name="twitter:image" content="'.$fallback.'">', false);

        $this->assertFileExists(public_path('assets/images/luxgo/global/og-default.jpg'));
    }

    public function test_the_icon_files_the_head_points_at_are_all_present(): void
    {
        $html = $this->get(route('home', ['locale' => 'id']))->assertOk()->getContent();

        preg_match_all('~<link rel="(?:icon|apple-touch-icon)"[^>]*href="([^"]+)"~', $html, $matches);

        $this->assertGreaterThanOrEqual(6, count($matches[1]));

        foreach ($matches[1] as $href) {
            $path = public_path(ltrim(parse_url($href, PHP_URL_PATH), '/'));
            $this->assertFileExists($path);
        }
    }

    /* Structured data ------------------------------------------------------- */

    public function test_the_structured_data_is_valid_and_describes_the_real_company(): void
    {
        $html = $this->get(route('home', ['locale' => 'id']))->assertOk()->getContent();

        preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $html, $matches);
        $graph = json_decode($matches[1], true);

        $this->assertIsArray($graph, 'the JSON-LD did not parse');
        $this->assertSame('https://schema.org', $graph['@context']);

        $types = array_column($graph['@graph'], '@type');
        $this->assertContains('WebSite', $types);
        $this->assertContains('Organization', $types);

        $organization = $graph['@graph'][array_search('Organization', $types, true)];
        $this->assertSame('PT Dwimuria Investama Properti', $organization['legalName']);
        $this->assertSame('+6281112341234', $organization['telephone']);
        $this->assertSame('info@luxandgo.com', $organization['email']);
        $this->assertContains('https://www.instagram.com/luxandgo.id', $organization['sameAs']);
    }

    public function test_a_detail_the_company_has_not_given_is_left_out_rather_than_invented(): void
    {
        SiteSetting::where('key', 'tiktok_handle')->update(['value' => '']);
        SiteSetting::where('key', 'phone')->update(['value' => '']);

        $html = $this->get(route('home', ['locale' => 'id']))->assertOk()->getContent();
        preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $html, $matches);
        $graph = json_decode($matches[1], true);

        $types = array_column($graph['@graph'], '@type');
        $organization = $graph['@graph'][array_search('Organization', $types, true)];

        $this->assertArrayNotHasKey('telephone', $organization);
        $this->assertSame(['https://www.instagram.com/luxandgo.id'], $organization['sameAs']);
    }

    /* robots.txt and the sitemap -------------------------------------------- */

    public function test_robots_txt_opens_the_site_but_keeps_the_panel_out(): void
    {
        $response = $this->get('/robots.txt')->assertOk();

        $this->assertStringStartsWith('text/plain', $response->headers->get('Content-Type'));

        $body = $response->getContent();
        $this->assertStringContainsString('User-agent: *', $body);
        $this->assertStringContainsString('Disallow: /admin', $body);
        $this->assertStringContainsString('Sitemap: '.route('sitemap'), $body);

        /* Blocking these would stop Google rendering the pages at all. */
        foreach (['assets/css', 'assets/js', 'assets/images', 'storage'] as $needed) {
            $this->assertStringNotContainsString('Disallow: /'.$needed, $body);
        }
    }

    public function test_the_sitemap_is_valid_xml_listing_every_public_page_in_both_locales(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();
        $this->assertStringStartsWith('application/xml', $response->headers->get('Content-Type'));

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'the sitemap is not valid XML');

        $xml->registerXPathNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $locations = array_map('strval', $xml->xpath('//s:loc'));

        $this->assertSame(array_unique($locations), $locations, 'a URL is listed twice');
        $this->assertCount(count(self::PUBLIC_ROUTES) * 2, $locations);

        foreach (array_merge($this->urls('id'), $this->urls('en')) as $url) {
            $this->assertContains($url, $locations);
        }
    }

    public function test_neither_crawler_file_opens_a_session(): void
    {
        /* They are registered outside the web middleware group: a crawler has
           no session to keep, and a response carrying Set-Cookie with
           Cache-Control: private is one a CDN will refuse to hold. */
        foreach (['/sitemap.xml', '/robots.txt'] as $path) {
            $response = $this->get($path)->assertOk();

            $this->assertSame([], $response->headers->getCookies(), "$path set a cookie");
            $this->assertStringNotContainsString('private', (string) $response->headers->get('Cache-Control'));
            $this->assertStringContainsString('max-age=3600', (string) $response->headers->get('Cache-Control'));
        }
    }

    /**
     * @return string|null the lastmod the sitemap gives that URL
     */
    private function lastmodFor(string $url): ?string
    {
        $xml = simplexml_load_string($this->get('/sitemap.xml')->assertOk()->getContent());

        foreach ($xml->url as $entry) {
            if ((string) $entry->loc === $url) {
                return count($entry->lastmod) > 0 ? (string) $entry->lastmod : null;
            }
        }

        return null;
    }

    public function test_the_sitemap_dates_each_page_from_when_its_content_last_changed(): void
    {
        $url = route('membership', ['locale' => 'id']);
        $page = Page::where('key', 'membership')->firstOrFail();

        $edited = Carbon::parse('2026-12-25 10:11:12');
        $page->sections()->firstOrFail()
            ->translations()->where('locale', 'id')->firstOrFail()
            ->forceFill(['updated_at' => $edited])->saveQuietly();

        $this->assertSame($edited->toAtomString(), $this->lastmodFor($url));
    }

    public function test_an_edit_to_an_faq_answer_moves_the_date_of_the_page_that_shows_it(): void
    {
        /* A page is more than its sections. If the FAQ were not followed the
           date would sit still while the page visibly changed. */
        $url = route('membership', ['locale' => 'id']);
        $before = $this->lastmodFor($url);

        $edited = Carbon::parse('2027-01-02 03:04:05');
        FaqItem::firstOrFail()->translations()->where('locale', 'id')->firstOrFail()
            ->forceFill(['updated_at' => $edited])->saveQuietly();

        $this->assertNotSame($before, $this->lastmodFor($url));
        $this->assertSame($edited->toAtomString(), $this->lastmodFor($url));
    }

    public function test_an_edit_to_a_vehicle_moves_the_date_of_the_collection_page(): void
    {
        /* Vehicles belong to the collection page by design rather than by a
           foreign key, so the link is one the sitemap has to make itself. */
        $url = route('collection', ['locale' => 'id']);

        $edited = Carbon::parse('2027-02-03 04:05:06');
        Vehicle::firstOrFail()->translations()->where('locale', 'id')->firstOrFail()
            ->forceFill(['updated_at' => $edited])->saveQuietly();

        $this->assertSame($edited->toAtomString(), $this->lastmodFor($url));
    }

    public function test_a_page_with_nothing_editable_carries_no_date_rather_than_a_guess(): void
    {
        /* Google drops lastmod across a whole sitemap once it finds dates it
           cannot trust, so the legal pages give none at all. */
        $xml = simplexml_load_string($this->get('/sitemap.xml')->assertOk()->getContent());
        $xml->registerXPathNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        $legal = route('legal.terms', ['locale' => 'id']);
        $dated = 0;

        foreach ($xml->url as $url) {
            if ((string) $url->loc === $legal) {
                $this->assertCount(0, $url->lastmod);
            }
            if (count($url->lastmod) > 0) {
                $dated++;
            }
        }

        /* The six CMS pages in both locales, and nothing else. */
        $this->assertSame(12, $dated);
    }

    public function test_the_sitemap_lists_nothing_private(): void
    {
        $body = $this->get('/sitemap.xml')->assertOk()->getContent();

        foreach (['/admin', '/login', '/up', 'sitemap.xml', 'robots.txt'] as $private) {
            $this->assertStringNotContainsString('<loc>'.url($private), $body);
        }
    }

    public function test_a_page_switched_off_in_the_admin_drops_out_of_the_sitemap(): void
    {
        $page = Page::where('key', 'experience')->firstOrFail();
        SeoSetting::where('page_id', $page->id)->update(['is_indexable' => false]);

        $body = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringNotContainsString('<loc>'.route('experience', ['locale' => 'id']).'</loc>', $body);
        $this->assertStringContainsString('<loc>'.route('membership', ['locale' => 'id']).'</loc>', $body);

        /* The page itself still says so in its own head. */
        $this->get(route('experience', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    /* The 404 --------------------------------------------------------------- */

    public function test_a_missing_page_returns_404_in_the_locale_that_was_asked_for(): void
    {
        $this->get('/id/tidak-ada')->assertNotFound()->assertSee('Halaman Tidak Ditemukan');
        $this->get('/en/nothing-here')->assertNotFound()->assertSee('Page Not Found');

        /* No locale in the URL at all: it still renders rather than breaking on
           a link it cannot build. */
        $this->get('/sembarang')->assertNotFound()->assertSee('LUX&amp;GO', false);
    }

    public function test_the_404_is_kept_out_of_the_index_and_offers_a_way_back(): void
    {
        $response = $this->get('/id/tidak-ada')->assertNotFound();

        $response->assertSee('<meta name="robots" content="noindex, follow">', false);
        $response->assertSee('href="'.route('home', ['locale' => 'id']).'"', false);
    }

    /* Production readiness --------------------------------------------------- */

    public function test_no_public_page_leaks_configuration_or_credentials(): void
    {
        foreach ($this->urls() as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            foreach (['APP_KEY', 'DB_PASSWORD', 'base64:', 'Whoops', 'vendor/laravel/framework'] as $secret) {
                $this->assertStringNotContainsString($secret, $html, "$secret leaked on $url");
            }
        }
    }
}
