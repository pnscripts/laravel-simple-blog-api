<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_post_list_hides_drafts(): void
    {
        $user = User::factory()->create();

        Post::factory()->published()->create([
            'user_id' => $user->id,
            'title' => 'Visible published post',
        ]);

        Post::factory()->draft()->create([
            'user_id' => $user->id,
            'title' => 'Hidden draft post',
        ]);

        $response = $this->getJson('/api/posts');

        $response
            ->assertOk()
            ->assertJsonFragment(['title' => 'Visible published post'])
            ->assertJsonMissing(['title' => 'Hidden draft post']);

        $this->assertCount(1, $response->json('data'));
    }

    public function test_public_show_returns_published_posts_only(): void
    {
        $user = User::factory()->create();

        $published = Post::factory()->published()->create([
            'user_id' => $user->id,
            'slug' => 'published-article',
        ]);

        $draft = Post::factory()->draft()->create([
            'user_id' => $user->id,
            'slug' => 'draft-article',
        ]);

        $this->getJson('/api/posts/'.$published->slug)
            ->assertOk()
            ->assertJsonPath('data.slug', 'published-article');

        $this->getJson('/api/posts/'.$draft->slug)
            ->assertNotFound();
    }

    public function test_public_post_list_can_filter_by_category_slug(): void
    {
        $user = User::factory()->create();
        $laravel = Category::factory()->create(['slug' => 'laravel']);
        $php = Category::factory()->create(['slug' => 'php']);

        Post::factory()->published()->create([
            'user_id' => $user->id,
            'category_id' => $laravel->id,
            'title' => 'Laravel post',
        ]);

        Post::factory()->published()->create([
            'user_id' => $user->id,
            'category_id' => $php->id,
            'title' => 'PHP post',
        ]);

        $this->getJson('/api/posts?category=laravel')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Laravel post'])
            ->assertJsonMissing(['title' => 'PHP post']);
    }

    public function test_authentication_is_required_to_write_posts(): void
    {
        $post = Post::factory()->published()->create();

        $this->postJson('/api/posts', [
            'title' => 'Nope',
            'excerpt' => 'Nope',
            'body' => 'Nope',
            'status' => 'published',
        ])->assertUnauthorized();

        $this->putJson('/api/posts/'.$post->slug, [
            'title' => 'Nope',
            'excerpt' => 'Nope',
            'body' => 'Nope',
            'status' => 'published',
        ])->assertUnauthorized();

        $this->deleteJson('/api/posts/'.$post->slug)->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_a_post(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/posts', [
            'title' => 'Hello from the API',
            'excerpt' => 'A short excerpt.',
            'body' => 'The full post body.',
            'status' => 'published',
            'category_id' => $category->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.title', 'Hello from the API')
            ->assertJsonPath('data.status', 'published')
            ->assertJsonPath('data.author.id', $user->id);

        $this->assertDatabaseHas('posts', [
            'title' => 'Hello from the API',
            'user_id' => $user->id,
            'status' => Post::STATUS_PUBLISHED,
        ]);
    }
}
