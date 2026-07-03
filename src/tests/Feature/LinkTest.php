<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_create_link(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('links.store'), [
                'original_url' => 'https://example.com',
                'title' => 'Test Link',
            ]);

        $response->assertRedirect(route('links.index'));
        $this->assertDatabaseHas('links', [
            'user_id' => $this->user->id,
            'original_url' => 'https://example.com',
            'title' => 'Test Link',
        ]);
    }

    public function test_link_redirect_works(): void
    {
        $link = Link::factory()->create([
            'is_active' => true,
            'expires_at' => null,
        ]);

        $response = $this->get(route('link.redirect', $link->short_code));

        $response->assertRedirect($link->original_url);
        $this->assertDatabaseHas('clicks', [
            'link_id' => $link->id,
        ]);
        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'clicks_count' => 1,
        ]);
    }

    public function test_user_can_delete_link(): void
    {
        $link = Link::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('links.destroy', $link));

        $response->assertRedirect(route('links.index'));
        $this->assertDatabaseMissing('links', [
            'id' => $link->id,
        ]);
        $this->assertDatabaseMissing('clicks', [
            'link_id' => $link->id,
        ]);
    }

    public function test_user_cannot_see_other_users_links(): void
    {
        $otherUser = User::factory()->create();
        $link = Link::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('links.show', $link));

        $response->assertStatus(403);
    }
}
