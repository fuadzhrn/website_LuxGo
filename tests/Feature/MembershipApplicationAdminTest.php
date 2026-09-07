<?php

namespace Tests\Feature;

use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipApplicationAdminTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        return User::factory()->administrator()->create();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function application(array $attributes = []): MembershipApplication
    {
        return MembershipApplication::create(array_merge([
            'full_name' => 'Resky Maulidiah',
            'phone' => '0812-3456-7890',
            'email' => 'resky@example.test',
            'lots_interested' => 3,
            'message' => 'Saya tertarik dengan membership.',
            'locale' => 'id',
            'status' => 'new',
            'submitted_at' => now(),
        ], $attributes));
    }

    /* List ----------------------------------------------------------------- */

    public function test_the_list_shows_an_empty_state_when_nothing_has_been_submitted(): void
    {
        $this->actingAs($this->administrator())
            ->get(route('admin.applications'))
            ->assertOk()
            ->assertSee('No membership applications yet')
            ->assertSee('Applications submitted through the website will appear here.');
    }

    public function test_the_list_shows_the_submitted_record(): void
    {
        $this->application();

        $this->actingAs($this->administrator())
            ->get(route('admin.applications'))
            ->assertOk()
            ->assertSee('Resky Maulidiah')
            ->assertSee('0812-3456-7890')
            ->assertSee('resky@example.test')
            ->assertSee('Bahasa Indonesia')
            ->assertSee('New')
            ->assertSee(now()->format('d M Y'));
    }

    public function test_the_newest_submission_comes_first(): void
    {
        $this->application(['full_name' => 'Older Lead', 'submitted_at' => now()->subDays(3)]);
        $this->application(['full_name' => 'Newer Lead', 'submitted_at' => now()]);

        $body = $this->actingAs($this->administrator())
            ->get(route('admin.applications'))
            ->assertOk()
            ->getContent();

        $this->assertLessThan(strpos($body, 'Older Lead'), strpos($body, 'Newer Lead'));
    }

    public function test_a_submission_without_a_submitted_at_still_lists(): void
    {
        $this->application(['full_name' => 'No Timestamp', 'submitted_at' => null]);

        $this->actingAs($this->administrator())
            ->get(route('admin.applications'))
            ->assertOk()
            ->assertSee('No Timestamp')
            ->assertSee(now()->format('d M Y'));
    }

    public function test_the_list_paginates_and_keeps_the_query_string(): void
    {
        for ($i = 1; $i <= 30; $i++) {
            $this->application(['full_name' => "Lead {$i}", 'submitted_at' => now()->subMinutes($i)]);
        }

        $response = $this->actingAs($this->administrator())
            ->get(route('admin.applications', ['search' => 'Lead']))
            ->assertOk();

        $response->assertSee('Lead 1');
        $response->assertDontSee('Lead 30');
        $response->assertSee('search=Lead', false);

        $this->actingAs($this->administrator())
            ->get(route('admin.applications', ['search' => 'Lead', 'page' => 2]))
            ->assertOk()
            ->assertSee('Lead 30');
    }

    /* Search and filters --------------------------------------------------- */

    public function test_search_matches_name_phone_and_email(): void
    {
        $this->application(['full_name' => 'Resky Maulidiah', 'phone' => '0812-1111-1111', 'email' => 'resky@example.test']);
        $this->application(['full_name' => 'Other Person', 'phone' => '0899-2222-2222', 'email' => 'other@example.test']);

        $admin = $this->administrator();

        foreach (['resky', '0812', 'resky@example'] as $term) {
            $this->actingAs($admin)
                ->get(route('admin.applications', ['search' => $term]))
                ->assertOk()
                ->assertSee('Resky Maulidiah')
                ->assertDontSee('Other Person');
        }
    }

    public function test_the_status_and_language_filters_work_together_with_search(): void
    {
        $this->application(['full_name' => 'Target Lead', 'status' => 'contacted', 'locale' => 'id']);
        $this->application(['full_name' => 'Wrong Status', 'status' => 'new', 'locale' => 'id']);
        $this->application(['full_name' => 'Wrong Language', 'status' => 'contacted', 'locale' => 'en']);

        $this->actingAs($this->administrator())
            ->get(route('admin.applications', ['search' => 'Lead', 'status' => 'contacted', 'locale' => 'id']))
            ->assertOk()
            ->assertSee('Target Lead')
            ->assertDontSee('Wrong Status')
            ->assertDontSee('Wrong Language');
    }

    public function test_an_unknown_filter_value_is_ignored_rather_than_queried(): void
    {
        $this->application(['full_name' => 'Visible Lead']);

        $this->actingAs($this->administrator())
            ->get(route('admin.applications', ['status' => 'deleted', 'locale' => 'fr']))
            ->assertOk()
            ->assertSee('Visible Lead');
    }

    public function test_the_summary_counts_are_real(): void
    {
        $this->application(['status' => 'new']);
        $this->application(['status' => 'new']);
        $this->application(['status' => 'contacted']);
        $this->application(['status' => 'completed']);

        $this->actingAs($this->administrator())
            ->get(route('admin.applications'))
            ->assertOk()
            ->assertSeeInOrder(['4', 'Total', '2', 'New', '1', 'Contacted']);
    }

    /* Detail --------------------------------------------------------------- */

    public function test_the_detail_page_shows_everything_that_was_submitted(): void
    {
        $application = $this->application();

        $this->actingAs($this->administrator())
            ->get(route('admin.applications.show', $application))
            ->assertOk()
            ->assertSee('Resky Maulidiah')
            ->assertSee('0812-3456-7890')
            ->assertSee('resky@example.test')
            ->assertSee('3')
            ->assertSee('Bahasa Indonesia')
            ->assertSee('Saya tertarik dengan membership.')
            ->assertSee($application->submitted_at->format('d M Y, H:i'))
            ->assertSee('tel:+6281234567890', false)
            ->assertSee('mailto:resky@example.test', false);
    }

    public function test_an_empty_message_reads_as_such(): void
    {
        $application = $this->application(['message' => null]);

        $this->actingAs($this->administrator())
            ->get(route('admin.applications.show', $application))
            ->assertOk()
            ->assertSee('No message provided.');
    }

    public function test_a_message_is_escaped(): void
    {
        $application = $this->application(['message' => '<script>alert(1)</script>']);

        $this->actingAs($this->administrator())
            ->get(route('admin.applications.show', $application))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    /* Status --------------------------------------------------------------- */

    public function test_the_status_moves_through_its_states(): void
    {
        $admin = $this->administrator();
        $application = $this->application();

        foreach (['contacted', 'in_progress', 'completed'] as $status) {
            $this->actingAs($admin)
                ->patch(route('admin.applications.status', $application), ['status' => $status])
                ->assertRedirect(route('admin.applications.show', $application))
                ->assertSessionHas('success', 'Application status updated successfully.');

            $this->assertSame($status, $application->fresh()->status);
        }

        $rejected = $this->application();
        $this->actingAs($admin)->patch(route('admin.applications.status', $rejected), ['status' => 'rejected']);
        $this->assertSame('rejected', $rejected->fresh()->status);
    }

    public function test_an_invalid_status_is_rejected(): void
    {
        $application = $this->application();

        foreach (['deleted', '', 'NEW'] as $status) {
            $this->actingAs($this->administrator())
                ->patch(route('admin.applications.status', $application), ['status' => $status])
                ->assertSessionHasErrors('status');
        }

        $this->assertSame('new', $application->fresh()->status);
    }

    public function test_the_module_never_edits_the_submission_itself(): void
    {
        $application = $this->application();

        $this->actingAs($this->administrator())->patch(route('admin.applications.status', $application), [
            'status' => 'contacted',
            'full_name' => 'Changed Name',
            'email' => 'changed@example.test',
            'lots_interested' => 99,
        ]);

        $application->refresh();
        $this->assertSame('Resky Maulidiah', $application->full_name);
        $this->assertSame('resky@example.test', $application->email);
        $this->assertSame(3, $application->lots_interested);
        $this->assertSame('contacted', $application->status);
    }

    public function test_there_is_no_delete_route(): void
    {
        $application = $this->application();

        $this->actingAs($this->administrator())
            ->delete('/admin/applications/'.$application->id)
            ->assertStatus(405);

        $this->assertNotNull($application->fresh());
    }

    /* Access --------------------------------------------------------------- */

    public function test_a_guest_and_a_non_admin_cannot_reach_the_module(): void
    {
        $application = $this->application();

        $this->get(route('admin.applications'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.applications.show', $application))->assertRedirect(route('admin.login'));

        $user = User::factory()->create();
        $this->actingAs($user)->get(route('admin.applications'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.applications.show', $application))->assertForbidden();
        $this->actingAs($user)
            ->patch(route('admin.applications.status', $application), ['status' => 'contacted'])
            ->assertForbidden();

        $this->assertSame('new', $application->fresh()->status);
    }
}
