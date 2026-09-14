<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\MembershipSettingsSeeder;
use Database\Seeders\PageContentSeeder;
use Database\Seeders\PagesSeeder;
use Database\Seeders\SiteSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteSettingsAdminTest extends TestCase
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

    /**
     * The form posts every field it renders. This mirrors that.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        $stored = SiteSetting::query()->pluck('value', 'key')->all();

        return array_replace([
            'company_name' => $stored['company_name'] ?? '',
            'phone' => $stored['phone'] ?? '',
            'email' => $stored['email'] ?? '',
            'instagram_handle' => $stored['instagram_handle'] ?? '',
            'tiktok_handle' => $stored['tiktok_handle'] ?? '',
            'head_office_address' => $stored['head_office_address'] ?? '',
            'head_office_city' => $stored['head_office_city'] ?? '',
        ], $overrides);
    }

    private function value(string $key): ?string
    {
        return SiteSetting::where('key', $key)->value('value');
    }

    /* Screen --------------------------------------------------------------- */

    public function test_the_screen_shows_the_stored_details_and_the_links_built_from_them(): void
    {
        $this->actingAs($this->administrator())
            ->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('PT Dwimuria Investama Properti')
            ->assertSee('0811-1234-1234')
            ->assertSee('info@luxandgo.com')
            ->assertSee('@luxandgo.id')
            ->assertSee('Gajah Mada Tower, Lt. 19-01')
            ->assertSee('tel:+6281112341234')
            ->assertSee('https://www.instagram.com/luxandgo.id')
            ->assertSee('https://www.tiktok.com/@luxandgo.id');
    }

    public function test_the_shipped_handles_point_at_the_published_profiles(): void
    {
        $this->assertSame('@luxandgo.id', $this->value('instagram_handle'));
        $this->assertSame('@luxandgo.id', $this->value('tiktok_handle'));
    }

    /* Saving --------------------------------------------------------------- */

    public function test_an_administrator_can_change_every_detail(): void
    {
        $this->actingAs($this->administrator())
            ->put(route('admin.settings.update'), $this->payload([
                'company_name' => 'PT Contoh Mobilitas',
                'phone' => '0812-9999-8888',
                'email' => 'halo@luxandgo.com',
                'instagram_handle' => '@contoh.id',
                'tiktok_handle' => '@contoh.id',
                'head_office_address' => "Menara Contoh Lt. 5\nJl. Contoh No.1\nJakarta Selatan 12000",
                'head_office_city' => 'Jakarta Selatan',
            ]))
            ->assertRedirect(route('admin.settings'))
            ->assertSessionHas('success', 'Settings updated successfully.');

        $this->assertSame('PT Contoh Mobilitas', $this->value('company_name'));
        $this->assertSame('0812-9999-8888', $this->value('phone'));
        $this->assertSame('halo@luxandgo.com', $this->value('email'));
        $this->assertSame('Jakarta Selatan', $this->value('head_office_city'));
    }

    public function test_saving_updates_rows_rather_than_adding_them(): void
    {
        $before = SiteSetting::count();

        $this->actingAs($this->administrator())
            ->put(route('admin.settings.update'), $this->payload(['company_name' => 'PT Baru']));

        $this->assertSame($before, SiteSetting::count());
        $this->assertSame(1, SiteSetting::where('key', 'company_name')->count());
    }

    public function test_a_handle_is_stored_with_one_at_sign_however_it_is_typed(): void
    {
        $admin = $this->administrator();

        foreach (['luxandgo.id', '@luxandgo.id'] as $typed) {
            $this->actingAs($admin)->put(route('admin.settings.update'), $this->payload([
                'instagram_handle' => $typed,
            ]));

            $this->assertSame('@luxandgo.id', $this->value('instagram_handle'));
        }
    }

    /* Validation ----------------------------------------------------------- */

    public function test_invalid_details_are_rejected_and_nothing_is_saved(): void
    {
        $admin = $this->administrator();

        $cases = [
            ['company_name' => '', 'field' => 'company_name'],
            ['email' => 'bukan-email', 'field' => 'email'],
            ['phone' => 'telepon kami', 'field' => 'phone'],
            ['instagram_handle' => 'handle dengan spasi', 'field' => 'instagram_handle'],
            ['head_office_address' => '', 'field' => 'head_office_address'],
        ];

        foreach ($cases as $case) {
            $field = $case['field'];
            unset($case['field']);

            $this->actingAs($admin)
                ->put(route('admin.settings.update'), $this->payload($case))
                ->assertSessionHasErrors($field);
        }

        $this->assertSame('PT Dwimuria Investama Properti', $this->value('company_name'));
        $this->assertSame('info@luxandgo.com', $this->value('email'));
    }

    /* Public output -------------------------------------------------------- */

    public function test_one_change_reaches_every_place_the_site_shows_it(): void
    {
        $this->actingAs($this->administrator())->put(route('admin.settings.update'), $this->payload([
            'company_name' => 'PT Contoh Mobilitas',
            'email' => 'halo@luxandgo.com',
            'phone' => '0812-9999-8888',
            'head_office_city' => 'Bandung',
        ]));

        /* The footer is on every page. */
        foreach (['home', 'membership'] as $route) {
            $this->get(route($route, ['locale' => 'id']))
                ->assertOk()
                ->assertSee('halo@luxandgo.com')
                ->assertSee('0812-9999-8888')
                ->assertSee('Bandung')
                ->assertSee('PT Contoh Mobilitas')
                ->assertDontSee('info@luxandgo.com');
        }

        /* The contact section and the legal pages read the same values. */
        $this->get(route('about', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('halo@luxandgo.com')
            ->assertSee('PT Contoh Mobilitas');

        $this->get(route('legal.terms', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('halo@luxandgo.com');
    }

    public function test_the_links_are_rebuilt_from_the_new_details(): void
    {
        $this->actingAs($this->administrator())->put(route('admin.settings.update'), $this->payload([
            'phone' => '0812-9999-8888',
            'instagram_handle' => 'contoh.id',
            'tiktok_handle' => '@contoh.id',
        ]));

        $this->get(route('about', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('tel:+6281299998888', false)
            ->assertSee('https://www.instagram.com/contoh.id', false)
            ->assertSee('https://www.tiktok.com/@contoh.id', false);
    }

    public function test_an_empty_handle_hides_that_channel(): void
    {
        $this->actingAs($this->administrator())->put(route('admin.settings.update'), $this->payload([
            'tiktok_handle' => '',
        ]));

        $this->get(route('about', ['locale' => 'id']))
            ->assertOk()
            ->assertDontSee('https://www.tiktok.com', false);

        $this->get(route('home', ['locale' => 'id']))
            ->assertOk()
            ->assertDontSee('https://www.tiktok.com', false);
    }

    public function test_the_address_prints_one_line_per_row(): void
    {
        $this->actingAs($this->administrator())->put(route('admin.settings.update'), $this->payload([
            'head_office_address' => "Menara Contoh Lt. 5\nJl. Contoh No.1\nJakarta Selatan 12000",
        ]));

        $response = $this->get(route('about', ['locale' => 'id']))->assertOk();

        foreach (['Menara Contoh Lt. 5', 'Jl. Contoh No.1', 'Jakarta Selatan 12000'] as $line) {
            $response->assertSee($line);
        }

        $this->assertSame(3, substr_count($response->getContent(), 'about-contact__address-line'));
    }

    /* Access --------------------------------------------------------------- */

    public function test_a_guest_and_a_non_admin_cannot_reach_or_change_the_settings(): void
    {
        $this->get(route('admin.settings'))->assertRedirect(route('admin.login'));

        $user = User::factory()->create();
        $this->actingAs($user)->get(route('admin.settings'))->assertForbidden();
        $this->actingAs($user)
            ->put(route('admin.settings.update'), $this->payload(['company_name' => 'Diubah']))
            ->assertForbidden();

        $this->assertSame('PT Dwimuria Investama Properti', $this->value('company_name'));
    }
}
