<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'API User',
            'email' => 'api@example.com',
        ]);

        $laravel = Category::factory()->create([
            'name' => 'Laravel',
            'slug' => 'laravel',
            'description' => 'Posts about the Laravel framework.',
        ]);

        $php = Category::factory()->create([
            'name' => 'PHP',
            'slug' => 'php',
            'description' => 'Posts about the PHP language.',
        ]);

        Post::factory()->published()->create([
            'user_id' => $user->id,
            'category_id' => $laravel->id,
            'title' => 'Getting started with this API',
            'slug' => 'getting-started-with-this-api',
            'excerpt' => 'How to authenticate and list published posts.',
            'body' => 'Register or log in to receive a Sanctum token, then create posts against the authenticated endpoints.',
        ]);

        Post::factory()->published()->create([
            'user_id' => $user->id,
            'category_id' => $laravel->id,
            'title' => 'Publishing a Laravel post',
            'slug' => 'publishing-a-laravel-post',
            'excerpt' => 'Draft vs published status for public listing.',
            'body' => 'Only posts with a published status are returned by the public index and show endpoints.',
        ]);

        Post::factory()->published()->create([
            'user_id' => $user->id,
            'category_id' => $php->id,
            'title' => 'Why PHP still works for APIs',
            'slug' => 'why-php-still-works-for-apis',
            'excerpt' => 'A short note on PHP and JSON APIs.',
            'body' => 'This starter keeps the stack small: Laravel 13, Eloquent, and token auth.',
        ]);

        Post::factory()->published()->create([
            'user_id' => $user->id,
            'category_id' => $php->id,
            'title' => 'Filtering posts by category',
            'slug' => 'filtering-posts-by-category',
            'excerpt' => 'Use the category query string on GET /api/posts.',
            'body' => 'Pass ?category=laravel or ?category=php to limit the public list to that category slug.',
        ]);

        Post::factory()->draft()->create([
            'user_id' => $user->id,
            'category_id' => $laravel->id,
            'title' => 'Unpublished notes',
            'slug' => 'unpublished-notes',
            'excerpt' => 'This draft should not appear on the public list.',
            'body' => 'Draft posts are stored for authenticated writers and omitted from public GET endpoints.',
        ]);
    }
}
