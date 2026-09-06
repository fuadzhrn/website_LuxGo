<?php

namespace Tests\Feature;

use App\Models\FaqItem;
use App\Models\MembershipSetting;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;
use App\Services\PageContentService;
use App\Support\MembershipValues;
use Database\Seeders\MembershipSettingsSeeder;
use Database\Seeders\PageContentSeeder;
use Database\Seeders\PagesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MembershipContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed(PagesSeeder::class);
        $this->seed(MembershipSettingsSeeder::class);
        $this->seed(PageContentSeeder::class);
    }

    private function administrator(): User
    {
        return User::factory()->administrator()->create();
    }

    private function membership(): Page
    {
        return Page::where('key', 'membership')->sole();
    }

    private function section(string $key): PageSection
    {
        return $this->membership()->sections()->where('section_key', $key)->sole();
    }

    private function settings(): MembershipSetting
    {
        return MembershipSetting::query()->orderBy('id')->sole();
    }

    private function values(): MembershipValues
    {
        return new MembershipValues($this->settings()->fresh());
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
        $definition = config("page_content.pages.membership.sections.{$sectionKey}");
        $service = app(PageContentService::class);
        $data = ['is_active' => 1];

        foreach (config('locales.supported') as $locale) {
            $content = $service->editableContent('membership', $sectionKey, $locale);

            foreach (array_keys($definition['fields']) as $path) {
                data_set($data, "content.{$locale}.{$path}", data_get($content, $path));
            }
        }

        foreach ($definition['settings'] ?? [] as $key => $setting) {
            $data['settings'][$key] = $setting['default'] ?? null;
        }

        return array_replace_recursive($data, $overrides);
    }

    /**
     * @return array<string, mixed>
     */
    private function settingsPayload(array $overrides = []): array
    {
        return array_replace($this->settings()->only([
            'regular_membership_price', 'promo_membership_price', 'promo_member_limit',
            'membership_period_years', 'base_usage_rights_per_year', 'additional_lot_rights_per_year',
            'member_usage_fee', 'additional_usage_fee', 'usage_duration_hours',
        ]), $overrides);
    }

    /* Business settings ---------------------------------------------------- */

    public function test_the_published_figures_are_the_ones_in_the_database(): void
    {
        $settings = $this->settings();

        $this->assertSame(35_000_000, $settings->regular_membership_price);
        $this->assertSame(25_000_000, $settings->promo_membership_price);
        $this->assertSame(100, $settings->promo_member_limit);
        $this->assertSame(5, $settings->membership_period_years);
        $this->assertSame(6, $settings->base_usage_rights_per_year);
        $this->assertSame(2, $settings->additional_lot_rights_per_year);
        $this->assertSame(750_000, $settings->member_usage_fee);
        $this->assertSame(500_000, $settings->additional_usage_fee);
        $this->assertSame(12, $settings->usage_duration_hours);
    }

    public function test_the_derived_figures_are_calculated_not_stored(): void
    {
        $values = $this->values();

        $this->assertSame(30, $values->totalMembershipRights());
        $this->assertSame(1_250_000, $values->additionalUsageTotal());
        $this->assertSame('Rp1.250.000', $values->additionalUsageTotalFormatted());
        $this->assertSame('Rp35.000.000', $values->regularPrice());

        /* Nothing derived is kept as a column of its own. */
        $this->assertArrayNotHasKey('total_usage_rights', $this->settings()->getAttributes());
        $this->assertArrayNotHasKey('additional_lot_price', $this->settings()->getAttributes());
    }

    public function test_usage_rights_follow_the_lot_rule(): void
    {
        $values = $this->values();

        $this->assertSame(6, $values->annualRightsFor(1));
        $this->assertSame(30, $values->totalRightsFor(1));
        $this->assertSame(14, $values->annualRightsFor(5));
        $this->assertSame(70, $values->totalRightsFor(5));
        $this->assertSame(24, $values->annualRightsFor(10));
        $this->assertSame(120, $values->totalRightsFor(10));
    }

    public function test_changed_settings_change_every_calculated_figure(): void
    {
        $this->settings()->update([
            'base_usage_rights_per_year' => 8,
            'additional_lot_rights_per_year' => 3,
            'membership_period_years' => 4,
        ]);

        $values = $this->values();

        $this->assertSame(8, $values->annualRightsFor(1));
        $this->assertSame(32, $values->totalRightsFor(1));
        $this->assertSame(20, $values->annualRightsFor(5));
        $this->assertSame(80, $values->totalRightsFor(5));
        $this->assertSame(32, $values->totalMembershipRights());
    }

    public function test_an_administrator_can_edit_the_figures(): void
    {
        $this->actingAs($this->administrator())
            ->put(route('admin.content.business-settings.update', $this->membership()), $this->settingsPayload([
                'member_usage_fee' => 800_000,
                'additional_usage_fee' => 600_000,
            ]))
            ->assertRedirect(route('admin.content.business-settings', $this->membership()))
            ->assertSessionHas('success', 'Changes saved successfully.');

        $this->assertSame(800_000, $this->settings()->fresh()->member_usage_fee);
        $this->assertSame(1_400_000, $this->values()->additionalUsageTotal());
    }

    public function test_negative_and_zero_figures_are_rejected(): void
    {
        $this->actingAs($this->administrator())
            ->put(route('admin.content.business-settings.update', $this->membership()), $this->settingsPayload([
                'regular_membership_price' => -1,
                'membership_period_years' => 0,
                'base_usage_rights_per_year' => 0,
            ]))
            ->assertSessionHasErrors(['regular_membership_price', 'membership_period_years', 'base_usage_rights_per_year']);

        $this->assertSame(35_000_000, $this->settings()->fresh()->regular_membership_price);
    }

    public function test_a_page_without_business_settings_has_no_such_screen(): void
    {
        $this->actingAs($this->administrator())
            ->get(route('admin.content.business-settings', Page::where('key', 'home')->sole()))
            ->assertNotFound();
    }

    /* Public page ---------------------------------------------------------- */

    public function test_the_public_page_shows_the_figures_from_the_database(): void
    {
        $response = $this->get(route('membership', ['locale' => 'id']))->assertOk();

        $response->assertSee('Rp35.000.000');
        $response->assertSee('Rp25.000.000');
        $response->assertSee('Rp750.000');
        $response->assertSee('Rp1.250.000');
        $response->assertSee('data-base-rights="6"', false);
        $response->assertSee('data-additional-rights="2"', false);
        $response->assertSee('data-period="5"', false);
    }

    public function test_changing_a_setting_updates_the_whole_page(): void
    {
        $this->settings()->update([
            'regular_membership_price' => 40_000_000,
            'base_usage_rights_per_year' => 8,
            'additional_lot_rights_per_year' => 3,
            'membership_period_years' => 4,
            'usage_duration_hours' => 10,
        ]);

        $response = $this->get(route('membership', ['locale' => 'id']))->assertOk();

        $response->assertSee('Rp40.000.000');
        $response->assertSee('data-base-rights="8"', false);
        $response->assertSee('data-additional-rights="3"', false);
        $response->assertSee('data-period="4"', false);

        /* Copy that mentions a figure follows it too. */
        $response->assertSee('Mulai dengan 8 Hak Pakai per tahun');
        $response->assertSee('/ 10 Jam');
        $response->assertSee('32 Hak Pakai selama 4 tahun');
        $response->assertDontSee('Rp35.000.000');
    }

    public function test_the_worked_examples_use_the_same_rule_as_the_calculator(): void
    {
        $response = $this->get(route('membership', ['locale' => 'en']))->assertOk();

        /* 1, 5 and 10 LOT: 6, 14 and 24 rights per year. */
        $response->assertSee('1 LOT');
        $response->assertSee('5 LOT');
        $response->assertSee('10 LOT');
        $response->assertSee('14×');
        $response->assertSee('24×');
    }

    public function test_the_page_never_shows_a_raw_placeholder(): void
    {
        foreach (['id', 'en'] as $locale) {
            $this->get(route('membership', ['locale' => $locale]))
                ->assertOk()
                ->assertDontSee('{{')
                ->assertDontSee('membership.faq.');
        }
    }

    public function test_the_page_reads_the_database_in_both_locales(): void
    {
        $hero = $this->section('hero');

        foreach (['id' => 'Membership Baru', 'en' => 'A New Membership'] as $locale => $heading) {
            $translation = $hero->translation($locale);
            $translation->content = array_merge($translation->content, ['title_1' => $heading]);
            $translation->save();
        }

        $this->get(route('membership', ['locale' => 'id']))->assertOk()->assertSee('Membership Baru');
        $this->get(route('membership', ['locale' => 'en']))->assertOk()->assertSee('A New Membership');
    }

    public function test_a_missing_indonesian_line_falls_back_to_english(): void
    {
        $hero = $this->section('hero');

        $english = $hero->translation('en');
        $english->content = array_merge($english->content, ['copy' => 'English fallback copy.']);
        $english->save();

        $indonesian = $hero->translation('id');
        $content = $indonesian->content;
        unset($content['copy']);
        $indonesian->content = $content;
        $indonesian->save();

        $this->get(route('membership', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('English fallback copy.');
    }

    public function test_an_inactive_section_is_hidden_and_returns_when_re_activated(): void
    {
        $section = $this->section('more_lot');

        $section->update(['is_active' => false]);
        $this->get(route('membership', ['locale' => 'id']))->assertOk()->assertDontSee('data-calculator', false);

        $section->update(['is_active' => true]);
        $this->get(route('membership', ['locale' => 'id']))->assertOk()->assertSee('data-calculator', false);
    }

    public function test_the_cta_keeps_the_visitor_locale_when_it_points_at_a_page(): void
    {
        $this->actingAs($this->administrator())->put(
            route('admin.content.section.update', [$this->membership(), $this->section('faq_cta')]),
            $this->payload('faq_cta', ['settings' => ['cta_target' => 'about']])
        )->assertSessionHasNoErrors();

        $this->get(route('membership', ['locale' => 'id']))->assertOk()->assertSee('/id/about');
        $this->get(route('membership', ['locale' => 'en']))->assertOk()->assertSee('/en/about');
    }

    /* Section editor ------------------------------------------------------- */

    public function test_the_membership_page_lists_its_five_sections(): void
    {
        $response = $this->actingAs($this->administrator())
            ->get(route('admin.content.page', $this->membership()))
            ->assertOk();

        foreach (['Hero', 'Membership Package', 'More LOT. More Access', 'Understanding Your Usage', 'FAQ + Closing CTA'] as $label) {
            $response->assertSee($label);
        }

        $response->assertSee('Edit business settings');
        $this->assertCount(5, config('page_content.pages.membership.sections'));
    }

    public function test_saving_a_section_stores_both_languages(): void
    {
        $this->actingAs($this->administrator())->put(
            route('admin.content.section.update', [$this->membership(), $this->section('hero')]),
            $this->payload('hero', [
                'content' => [
                    'id' => ['title_1' => 'Satu Membership Baru.'],
                    'en' => ['title_1' => 'One New Membership.'],
                ],
            ])
        )->assertSessionHas('success', 'Changes saved successfully.');

        $hero = $this->section('hero');
        $this->assertSame('Satu Membership Baru.', $hero->translation('id')->content['title_1']);
        $this->assertSame('One New Membership.', $hero->translation('en')->content['title_1']);
    }

    public function test_copy_may_use_a_known_placeholder_but_not_an_invented_one(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)->put(
            route('admin.content.section.update', [$this->membership(), $this->section('usage')]),
            $this->payload('usage', ['content' => ['id' => ['unit' => '/ {{usage_duration}} Jam']]])
        )->assertSessionHasNoErrors();

        $this->actingAs($admin)->put(
            route('admin.content.section.update', [$this->membership(), $this->section('usage')]),
            $this->payload('usage', ['content' => ['id' => ['unit' => '/ {{lot_price}} Jam']]])
        )->assertSessionHasErrors('content.id.unit');
    }

    /* FAQ ------------------------------------------------------------------ */

    public function test_the_existing_questions_were_moved_into_the_database(): void
    {
        $items = $this->section('faq_cta')->faqItems()->get();

        $this->assertCount(7, $items);
        $this->assertSame('Apa itu LOT Membership LUX&GO?', $items->first()->translation('id')->question);
        $this->assertSame('What is a LUX&GO LOT Membership?', $items->first()->translation('en')->question);
        $this->assertTrue($items->last()->shows_usage_breakdown);
    }

    public function test_seeding_again_does_not_duplicate_the_questions(): void
    {
        $this->seed(PageContentSeeder::class);

        $this->assertCount(7, $this->section('faq_cta')->faqItems()->get());
    }

    public function test_an_administrator_can_add_edit_reorder_and_delete_a_question(): void
    {
        $admin = $this->administrator();
        $section = $this->section('faq_cta');
        $route = fn (string $name, ...$extra) => route($name, [$this->membership(), $section, ...$extra]);

        /* Add */
        $this->actingAs($admin)->post($route('admin.content.faq.store'), [
            'is_active' => 1,
            'question' => ['id' => 'Pertanyaan baru?', 'en' => 'A new question?'],
            'answer' => ['id' => 'Jawaban baru.', 'en' => 'A new answer.'],
        ])->assertSessionHas('success', 'Changes saved successfully.');

        $added = $section->faqItems()->get()->last();
        $this->assertSame('Pertanyaan baru?', $added->translation('id')->question);
        $this->assertSame(8, $section->faqItems()->count());

        /* Edit */
        $this->actingAs($admin)->put($route('admin.content.faq.update', $added), [
            'is_active' => 1,
            'question' => ['id' => 'Pertanyaan diubah?', 'en' => 'An edited question?'],
            'answer' => ['id' => 'Jawaban diubah.', 'en' => 'An edited answer.'],
        ])->assertSessionHasNoErrors();

        $this->assertSame('Pertanyaan diubah?', $added->fresh()->translation('id')->question);

        /* Reorder */
        $this->actingAs($admin)->post($route('admin.content.faq.move', $added), ['direction' => 'up']);
        $this->assertTrue($section->faqItems()->get()->get(6)->is($added));

        /* Delete */
        $this->actingAs($admin)->delete($route('admin.content.faq.destroy', $added));
        $this->assertSame(7, $section->faqItems()->count());
        $this->assertNull(FaqItem::find($added->id));
    }

    public function test_a_question_requires_both_languages(): void
    {
        $this->actingAs($this->administrator())
            ->post(route('admin.content.faq.store', [$this->membership(), $this->section('faq_cta')]), [
                'is_active' => 1,
                'question' => ['id' => 'Hanya Indonesia?', 'en' => ''],
                'answer' => ['id' => 'Jawaban.', 'en' => 'Answer.'],
            ])
            ->assertSessionHasErrors('question.en');

        $this->assertSame(7, $this->section('faq_cta')->faqItems()->count());
    }

    public function test_an_inactive_question_is_not_rendered(): void
    {
        $item = $this->section('faq_cta')->faqItems()->first();
        $question = $item->translation('id')->question;

        $this->get(route('membership', ['locale' => 'id']))->assertOk()->assertSee($question);

        $item->update(['is_active' => false]);

        $this->get(route('membership', ['locale' => 'id']))->assertOk()->assertDontSee($question);
    }

    public function test_a_faq_answer_renders_the_current_figures(): void
    {
        $response = $this->get(route('membership', ['locale' => 'id']))->assertOk();
        $response->assertSee('6 Hak Pakai per tahun, setara 30 Hak Pakai selama 5 tahun');

        $this->settings()->update(['base_usage_rights_per_year' => 7]);

        $this->get(route('membership', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('7 Hak Pakai per tahun, setara 35 Hak Pakai selama 5 tahun');
    }

    public function test_a_faq_screen_only_exists_for_a_section_that_has_one(): void
    {
        $this->actingAs($this->administrator())
            ->get(route('admin.content.faq', [$this->membership(), $this->section('hero')]))
            ->assertNotFound();
    }

    /* Security ------------------------------------------------------------- */

    public function test_a_guest_and_a_non_admin_cannot_reach_or_change_the_membership_cms(): void
    {
        $settingsUrl = route('admin.content.business-settings', $this->membership());
        $faqUrl = route('admin.content.faq', [$this->membership(), $this->section('faq_cta')]);

        $this->get($settingsUrl)->assertRedirect(route('admin.login'));
        $this->get($faqUrl)->assertRedirect(route('admin.login'));

        $user = User::factory()->create();
        $this->actingAs($user)->get($settingsUrl)->assertForbidden();
        $this->actingAs($user)->get($faqUrl)->assertForbidden();
        $this->actingAs($user)->put(route('admin.content.business-settings.update', $this->membership()), $this->settingsPayload())->assertForbidden();

        $this->assertSame(35_000_000, $this->settings()->fresh()->regular_membership_price);
    }
}
