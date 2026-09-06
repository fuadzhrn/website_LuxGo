<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Page;
use App\Models\SeoSetting;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    private function administrator(): User
    {
        return User::factory()->administrator()->create();
    }

    private function upload(User $user, string $name, int $width = 40, int $height = 40): Media
    {
        $this->actingAs($user)->post(route('admin.media.store'), [
            'file' => UploadedFile::fake()->image($name, $width, $height),
        ]);

        return Media::latest('id')->firstOrFail();
    }

    public function test_administrator_can_upload_a_jpeg(): void
    {
        $response = $this->actingAs($this->administrator())
            ->post(route('admin.media.store'), [
                'file' => UploadedFile::fake()->image('hero.jpg', 800, 600),
            ]);

        $media = Media::sole();

        $response->assertRedirect(route('admin.media', ['selected' => $media->id]));
        $this->assertSame('hero.jpg', $media->filename);
        $this->assertSame(800, $media->width);
        $this->assertSame(600, $media->height);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_administrator_can_upload_a_png_and_a_webp(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)->post(route('admin.media.store'), [
            'file' => UploadedFile::fake()->image('badge.png', 200, 200),
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->post(route('admin.media.store'), [
            'file' => UploadedFile::fake()->image('cover.webp', 320, 240),
        ])->assertSessionHasNoErrors();

        $this->assertSame(['png', 'webp'], Media::orderBy('id')->pluck('extension')->all());
    }

    public function test_stored_filename_is_never_the_client_filename(): void
    {
        $media = $this->upload($this->administrator(), 'evil name.jpg');

        $this->assertStringNotContainsString('evil name', $media->path);
        $this->assertStringStartsWith('luxgo/media/', $media->path);
    }

    public function test_a_non_image_file_is_rejected(): void
    {
        $this->actingAs($this->administrator())
            ->post(route('admin.media.store'), [
                'file' => UploadedFile::fake()->create('notes.txt', 8, 'text/plain'),
            ])
            ->assertSessionHasErrors('file');

        $this->assertSame(0, Media::count());
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_a_file_renamed_to_an_image_extension_is_rejected(): void
    {
        /* A real file rather than a fake one: the point of the check is that the
           contents are sniffed instead of the name and the declared type being
           taken at face value. */
        $path = tempnam(sys_get_temp_dir(), 'luxgo').'.jpg';
        file_put_contents($path, "<?php echo 'not an image';");

        $this->actingAs($this->administrator())
            ->post(route('admin.media.store'), [
                'file' => new UploadedFile($path, 'payload.jpg', 'image/jpeg', null, true),
            ])
            ->assertSessionHasErrors('file');

        $this->assertSame(0, Media::count());
        @unlink($path);
    }

    public function test_an_oversized_file_is_rejected(): void
    {
        $limit = (int) config('admin.images.max_kilobytes');

        $this->actingAs($this->administrator())
            ->post(route('admin.media.store'), [
                'file' => UploadedFile::fake()->create('huge.jpg', $limit + 1024, 'image/jpeg'),
            ])
            ->assertSessionHasErrors('file');

        $this->assertSame(0, Media::count());
    }

    public function test_alt_text_can_be_updated(): void
    {
        $admin = $this->administrator();
        $media = $this->upload($admin, 'hero.jpg');

        $this->actingAs($admin)
            ->patch(route('admin.media.update', $media), ['alt_text' => 'Denza D9 at dusk'])
            ->assertRedirect(route('admin.media', ['selected' => $media->id]));

        $this->assertSame('Denza D9 at dusk', $media->refresh()->alt_text);
    }

    public function test_unused_media_can_be_deleted_with_its_file(): void
    {
        $admin = $this->administrator();
        $media = $this->upload($admin, 'hero.jpg');
        $path = $media->path;

        $this->actingAs($admin)
            ->delete(route('admin.media.destroy', $media))
            ->assertRedirect(route('admin.media'));

        $this->assertSame(0, Media::count());
        Storage::disk('public')->assertMissing($path);
    }

    public function test_media_referenced_by_a_vehicle_cannot_be_deleted(): void
    {
        $admin = $this->administrator();
        $media = $this->upload($admin, 'hero.jpg');

        Vehicle::create([
            'name' => 'Denza D9',
            'slug' => 'denza-d9',
            'main_media_id' => $media->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.media.destroy', $media))
            ->assertSessionHas('error', 'This media is currently in use and cannot be deleted.');

        $this->assertSame(1, Media::count());
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_media_referenced_by_seo_settings_cannot_be_deleted(): void
    {
        $admin = $this->administrator();
        $media = $this->upload($admin, 'og.jpg');

        $page = Page::create(['key' => 'home', 'slug' => 'home']);

        SeoSetting::create(['page_id' => $page->id, 'og_media_id' => $media->id]);

        $this->actingAs($admin)->delete(route('admin.media.destroy', $media));

        $this->assertSame(1, Media::count());
    }

    public function test_a_guest_cannot_reach_or_change_the_library(): void
    {
        $this->get(route('admin.media'))->assertRedirect(route('admin.login'));

        $this->post(route('admin.media.store'), [
            'file' => UploadedFile::fake()->image('hero.jpg', 40, 40),
        ])->assertRedirect(route('admin.login'));

        $this->assertSame(0, Media::count());
    }

    public function test_a_non_admin_user_cannot_reach_or_change_the_library(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.media'))->assertForbidden();

        $this->actingAs($user)->post(route('admin.media.store'), [
            'file' => UploadedFile::fake()->image('hero.jpg', 40, 40),
        ])->assertForbidden();

        $this->assertSame(0, Media::count());
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_a_non_admin_user_cannot_delete_media(): void
    {
        $media = $this->upload($this->administrator(), 'hero.jpg');

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.media.destroy', $media))
            ->assertForbidden();

        $this->assertSame(1, Media::count());
    }

    public function test_an_empty_library_renders_its_empty_state(): void
    {
        $this->actingAs($this->administrator())
            ->get(route('admin.media'))
            ->assertOk()
            ->assertSee('No media uploaded yet');
    }

    public function test_the_library_can_be_searched_by_filename(): void
    {
        $admin = $this->administrator();

        $this->upload($admin, 'hero-lounge.jpg');
        $this->upload($admin, 'chauffeur.jpg');

        $this->actingAs($admin)
            ->get(route('admin.media', ['q' => 'hero']))
            ->assertOk()
            ->assertSee('hero-lounge.jpg')
            ->assertDontSee('chauffeur.jpg');
    }
}
