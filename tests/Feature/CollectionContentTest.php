<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\PageContentSeeder;
use Database\Seeders\PagesSeeder;
use Database\Seeders\VehicleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CollectionContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed(PagesSeeder::class);
        $this->seed(PageContentSeeder::class);
        $this->seed(VehicleSeeder::class);
    }

    private function administrator(): User
    {
        return User::factory()->administrator()->create();
    }

    private function denza(): Vehicle
    {
        return Vehicle::where('slug', 'denza-d9')->sole();
    }

    private function collection(): Page
    {
        return Page::where('key', 'collection')->sole();
    }

    private function insideSection(): PageSection
    {
        return $this->collection()->sections()->where('section_key', 'inside_experience')->sole();
    }

    /**
     * The vehicle form posts every field it renders. This mirrors that.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function vehiclePayload(?Vehicle $vehicle = null, array $overrides = []): array
    {
        $data = [
            'name' => $vehicle?->name ?? 'Test Vehicle',
            'slug' => $vehicle?->slug ?? 'test-vehicle',
            'is_active' => 1,
            'sort_order' => $vehicle?->sort_order ?? 1,
            'media_main_image_media_id' => $vehicle?->main_media_id,
        ];

        foreach (config('locales.supported') as $locale) {
            $content = $vehicle?->translation($locale)?->content ?? [];

            foreach (config('page_content.vehicle_fields') as $path => $field) {
                data_set($data, "content.{$locale}.{$path}", data_get($content, $path) ?? 'Copy '.$locale);
            }
        }

        return array_replace_recursive($data, $overrides);
    }

    /* Migration ------------------------------------------------------------ */

    public function test_the_existing_denza_content_moved_into_the_vehicle_record(): void
    {
        $vehicle = $this->denza();

        $this->assertSame('Denza D9', $vehicle->name);
        $this->assertTrue($vehicle->isActive());
        $this->assertSame(
            'Dirancang untuk mobilitas premium dengan kenyamanan halus, karakter listrik modern, dan kesan eksekutif.',
            $vehicle->translation('id')->content['tagline']
        );
        $this->assertSame('Premium Design', $vehicle->translation('en')->content['features']['design']);
        $this->assertSame('Desain Premium', $vehicle->translation('id')->content['features']['design']);
    }

    public function test_the_vehicle_imagery_moved_into_the_media_library(): void
    {
        $vehicle = $this->denza();

        $this->assertSame('denza-d9-main.webp', $vehicle->mainMedia->filename);
        $this->assertSame(
            ['interior-main.webp', 'interior-detail-01.webp', 'interior-detail-02.webp'],
            $vehicle->galleryMedia()->pluck('filename')->all()
        );
        Storage::disk('public')->assertExists($vehicle->mainMedia->path);
    }

    public function test_the_interior_section_points_at_the_vehicle_rather_than_copying_it(): void
    {
        $this->assertSame($this->denza()->id, $this->insideSection()->settings['vehicle_id']);
    }

    public function test_seeding_again_creates_no_second_denza(): void
    {
        $before = Media::count();

        $this->seed(VehicleSeeder::class);

        $this->assertSame(1, Vehicle::where('slug', 'denza-d9')->count());
        $this->assertSame(3, $this->denza()->galleryMedia()->count());
        $this->assertSame($before, Media::count());
    }

    /* Public page ---------------------------------------------------------- */

    public function test_the_public_page_reads_the_vehicle_in_both_locales(): void
    {
        $this->get(route('collection', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('Denza')
            ->assertSee('D9')
            ->assertSee('Desain Premium')
            ->assertSee('Kenyamanan Mewah');

        $this->get(route('collection', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('Premium Design')
            ->assertSee('Luxury Comfort');
    }

    public function test_a_missing_indonesian_line_falls_back_to_english(): void
    {
        $translation = $this->denza()->translation('id');
        $content = $translation->content;
        unset($content['tagline']);
        $translation->content = $content;
        $translation->save();

        $this->get(route('collection', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('Crafted for premium mobility with refined comfort, modern electric character, and executive presence.');
    }

    public function test_the_page_shows_the_gallery_from_the_referenced_vehicle(): void
    {
        $response = $this->get(route('collection', ['locale' => 'id']))->assertOk();

        foreach ($this->denza()->galleryMedia as $media) {
            $response->assertSee($media->url(), false);
        }
    }

    public function test_an_inactive_vehicle_is_hidden_and_returns_when_reactivated(): void
    {
        $vehicle = $this->denza();

        $vehicle->update(['status' => Vehicle::STATUS_INACTIVE]);
        $this->get(route('collection', ['locale' => 'id']))->assertOk()->assertDontSee('Desain Premium');

        /* Nothing was lost while it was off. */
        $this->assertSame(3, $vehicle->galleryMedia()->count());
        $this->assertNotNull($vehicle->translation('id'));

        $vehicle->update(['status' => Vehicle::STATUS_ACTIVE]);
        $this->get(route('collection', ['locale' => 'id']))->assertOk()->assertSee('Desain Premium');
    }

    public function test_a_second_active_vehicle_renders_in_sort_order(): void
    {
        $second = Vehicle::create([
            'name' => 'Second Vehicle',
            'slug' => 'second-vehicle',
            'status' => Vehicle::STATUS_ACTIVE,
            'sort_order' => 5,
        ]);

        $second->translations()->create([
            'locale' => 'en',
            'content' => ['tagline' => 'A second showcase.', 'features' => ['design' => 'Second Design']],
        ]);

        $response = $this->get(route('collection', ['locale' => 'en']))->assertOk();
        $response->assertSee('A second showcase.');

        /* Read from the taglines: the vehicle name also appears in the hero
           copy above, so it is not a marker for showcase order. */
        $denzaTagline = $this->denza()->translation('en')->content['tagline'];

        $this->assertLessThan(
            strpos($response->getContent(), 'A second showcase.'),
            strpos($response->getContent(), $denzaTagline)
        );

        /* Swapping the order, the way the list screen's arrows renumber it. */
        $second->update(['sort_order' => 1]);
        $this->denza()->update(['sort_order' => 2]);

        $reordered = $this->get(route('collection', ['locale' => 'en']))->assertOk()->getContent();

        $this->assertLessThan(strpos($reordered, $denzaTagline), strpos($reordered, 'A second showcase.'));
    }

    public function test_the_page_survives_a_vehicle_without_content_or_imagery(): void
    {
        Vehicle::create([
            'name' => 'Bare Vehicle',
            'slug' => 'bare-vehicle',
            'status' => Vehicle::STATUS_ACTIVE,
            'sort_order' => 9,
        ]);

        $this->get(route('collection', ['locale' => 'id']))
            ->assertOk()
            ->assertSee('Bare')
            ->assertSee('Vehicle');
    }

    /* Vehicle CRUD --------------------------------------------------------- */

    public function test_the_vehicle_list_shows_the_real_records(): void
    {
        $this->actingAs($this->administrator())
            ->get(route('admin.vehicles'))
            ->assertOk()
            ->assertSee('Denza D9')
            ->assertSee('denza-d9')
            ->assertSee('Active');
    }

    public function test_an_administrator_can_create_a_vehicle_with_both_translations(): void
    {
        $this->actingAs($this->administrator())
            ->post(route('admin.vehicles.store'), $this->vehiclePayload(null, [
                'name' => 'Denza D9 Long',
                'slug' => 'denza-d9-long',
                'content' => [
                    'id' => ['tagline' => 'Tagline Indonesia.'],
                    'en' => ['tagline' => 'English tagline.'],
                ],
                'media_main_image' => UploadedFile::fake()->image('new-vehicle.jpg', 800, 600),
            ]))
            ->assertSessionHasNoErrors();

        $vehicle = Vehicle::where('slug', 'denza-d9-long')->sole();

        $this->assertSame('Tagline Indonesia.', $vehicle->translation('id')->content['tagline']);
        $this->assertSame('English tagline.', $vehicle->translation('en')->content['tagline']);
        $this->assertSame('new-vehicle.jpg', $vehicle->mainMedia->filename);
        Storage::disk('public')->assertExists($vehicle->mainMedia->path);
    }

    public function test_an_administrator_can_edit_shared_and_translated_values(): void
    {
        $vehicle = $this->denza();

        $this->actingAs($this->administrator())
            ->put(route('admin.vehicles.update', $vehicle), $this->vehiclePayload($vehicle, [
                'name' => 'Denza D9 Premium',
                'content' => [
                    'id' => ['features' => ['design' => 'Desain Baru']],
                    'en' => ['features' => ['design' => 'New Design']],
                ],
            ]))
            ->assertSessionHas('success', 'Changes saved successfully.');

        $vehicle->refresh();
        $this->assertSame('Denza D9 Premium', $vehicle->name);
        $this->assertSame('Desain Baru', $vehicle->translation('id')->content['features']['design']);
        $this->assertSame('New Design', $vehicle->translation('en')->content['features']['design']);
    }

    public function test_the_slug_must_be_unique_and_url_safe(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->post(route('admin.vehicles.store'), $this->vehiclePayload(null, ['slug' => 'denza-d9']))
            ->assertSessionHasErrors('slug');

        $this->actingAs($admin)
            ->post(route('admin.vehicles.store'), $this->vehiclePayload(null, ['slug' => 'Not A Slug!']))
            ->assertSessionHasErrors('slug');

        $this->assertSame(1, Vehicle::count());
    }

    public function test_a_vehicle_can_be_deactivated_and_reactivated(): void
    {
        $admin = $this->administrator();
        $vehicle = $this->denza();

        $this->actingAs($admin)
            ->post(route('admin.vehicles.status', $vehicle), ['status' => 'inactive'])
            ->assertSessionHas('success', 'Vehicle deactivated.');

        $this->assertFalse($vehicle->fresh()->isActive());

        $this->actingAs($admin)->post(route('admin.vehicles.status', $vehicle), ['status' => 'active']);

        $this->assertTrue($vehicle->fresh()->isActive());
    }

    public function test_vehicles_can_be_reordered(): void
    {
        $second = Vehicle::create([
            'name' => 'Second Vehicle',
            'slug' => 'second-vehicle',
            'status' => Vehicle::STATUS_ACTIVE,
            'sort_order' => 5,
        ]);

        $this->actingAs($this->administrator())
            ->post(route('admin.vehicles.move', $second), ['direction' => 'up']);

        $this->assertSame(
            ['second-vehicle', 'denza-d9'],
            Vehicle::orderBy('sort_order')->orderBy('id')->pluck('slug')->all()
        );
    }

    /* Gallery -------------------------------------------------------------- */

    public function test_an_image_can_be_added_removed_and_reordered_in_the_gallery(): void
    {
        $admin = $this->administrator();
        $vehicle = $this->denza();

        /* Add by upload */
        $this->actingAs($admin)
            ->post(route('admin.vehicles.gallery.store', $vehicle), [
                'file' => UploadedFile::fake()->image('interior-extra.jpg', 600, 400),
            ])
            ->assertSessionHasNoErrors();

        $added = $vehicle->galleryMedia()->get()->last();
        $this->assertSame('interior-extra.jpg', $added->filename);
        $this->assertSame(4, $vehicle->galleryMedia()->count());

        /* Reorder */
        $this->actingAs($admin)->post(route('admin.vehicles.gallery.move', [$vehicle, $added->id]), ['direction' => 'up']);
        $this->assertSame(3, $vehicle->galleryMedia()->get()->search(fn ($m) => $m->is($added)) + 1);

        /* Remove: detaches only */
        $this->actingAs($admin)->delete(route('admin.vehicles.gallery.destroy', [$vehicle, $added->id]));

        $this->assertSame(3, $vehicle->galleryMedia()->count());
        $this->assertNotNull($added->fresh());
        Storage::disk('public')->assertExists($added->path);
    }

    public function test_an_existing_library_image_can_be_added_to_the_gallery(): void
    {
        $vehicle = $this->denza();
        $existing = Media::where('filename', 'denza-d9-main.webp')->sole();

        $this->actingAs($this->administrator())
            ->post(route('admin.vehicles.gallery.store', $vehicle), ['media_id' => $existing->id])
            ->assertSessionHasNoErrors();

        $this->assertTrue($vehicle->galleryMedia()->where('media_id', $existing->id)->exists());
    }

    /* Delete --------------------------------------------------------------- */

    public function test_a_vehicle_a_section_points_at_cannot_be_deleted(): void
    {
        $vehicle = $this->denza();

        $this->assertTrue($vehicle->isInUse());

        $this->actingAs($this->administrator())
            ->delete(route('admin.vehicles.destroy', $vehicle))
            ->assertSessionHas('error', 'This vehicle is currently in use. Deactivate it or change the referenced vehicle before deleting.');

        $this->assertNotNull($vehicle->fresh());
    }

    public function test_an_unused_vehicle_can_be_deleted_without_touching_the_media_library(): void
    {
        $vehicle = Vehicle::create([
            'name' => 'Spare Vehicle',
            'slug' => 'spare-vehicle',
            'status' => Vehicle::STATUS_INACTIVE,
            'sort_order' => 3,
        ]);

        $vehicle->translations()->create(['locale' => 'id', 'content' => ['tagline' => 'Sementara.']]);
        $media = Media::where('filename', 'interior-main.webp')->sole();
        $vehicle->galleryMedia()->attach($media->id, ['sort_order' => 1]);

        $mediaBefore = Media::count();

        $this->actingAs($this->administrator())
            ->delete(route('admin.vehicles.destroy', $vehicle))
            ->assertSessionHas('success', 'Vehicle deleted. Its images are still in the media library.');

        $this->assertNull(Vehicle::find($vehicle->id));
        $this->assertSame(0, $vehicle->translations()->count());
        $this->assertSame($mediaBefore, Media::count());
        Storage::disk('public')->assertExists($media->fresh()->path);
    }

    public function test_vehicle_media_cannot_be_deleted_from_the_library_while_it_is_used(): void
    {
        $admin = $this->administrator();
        $main = $this->denza()->mainMedia;
        $gallery = $this->denza()->galleryMedia()->first();

        foreach ([$main, $gallery] as $media) {
            $this->assertTrue($media->isInUse());

            $this->actingAs($admin)
                ->delete(route('admin.media.destroy', $media))
                ->assertSessionHas('error', 'This media is currently in use and cannot be deleted.');

            $this->assertNotNull($media->fresh());
        }
    }

    /* Page sections -------------------------------------------------------- */

    public function test_the_collection_page_lists_its_four_sections_and_the_vehicles(): void
    {
        $response = $this->actingAs($this->administrator())
            ->get(route('admin.content.page', $this->collection()))
            ->assertOk();

        foreach (['Collection Hero', 'Vehicle Showcase', 'Inside the Experience', 'Collection CTA'] as $label) {
            $response->assertSee($label);
        }

        $response->assertSee('Manage vehicles');
        $this->assertCount(4, config('page_content.pages.collection.sections'));
    }

    public function test_the_interior_section_editor_offers_the_vehicle_reference(): void
    {
        $this->actingAs($this->administrator())
            ->get(route('admin.content.section.edit', [$this->collection(), $this->insideSection()]))
            ->assertOk()
            ->assertSee('name="settings[vehicle_id]"', false)
            ->assertSee('Denza D9');
    }

    /* Security ------------------------------------------------------------- */

    public function test_a_guest_and_a_non_admin_cannot_reach_or_change_vehicles(): void
    {
        $vehicle = $this->denza();

        $this->get(route('admin.vehicles'))->assertRedirect(route('admin.login'));

        $user = User::factory()->create();
        $this->actingAs($user)->get(route('admin.vehicles'))->assertForbidden();
        $this->actingAs($user)->put(route('admin.vehicles.update', $vehicle), $this->vehiclePayload($vehicle))->assertForbidden();
        $this->actingAs($user)->delete(route('admin.vehicles.destroy', $vehicle))->assertForbidden();

        $this->assertSame('Denza D9', $vehicle->fresh()->name);
    }
}
