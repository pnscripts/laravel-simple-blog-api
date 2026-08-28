<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_are_public(): void
    {
        Category::factory()->create([
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);

        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonFragment(['slug' => 'laravel']);
    }

    public function test_authentication_is_required_to_write_categories(): void
    {
        $category = Category::factory()->create();

        $this->postJson('/api/categories', [
            'name' => 'Security',
        ])->assertUnauthorized();

        $this->putJson('/api/categories/'.$category->slug, [
            'name' => 'Renamed',
        ])->assertUnauthorized();

        $this->deleteJson('/api/categories/'.$category->slug)->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_a_category(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/categories', [
            'name' => 'Testing',
            'description' => 'Quality-related posts.',
        ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Testing')
            ->assertJsonPath('data.slug', 'testing');
    }
}
