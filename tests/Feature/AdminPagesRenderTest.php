<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_admin_pages_render_for_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create();
        $post->images()->create(['image_path' => 'posts/a.webp', 'alt' => 'Popis']);
        Post::factory()->draft()->create();
        Post::factory()->scheduled()->create();

        $pages = [
            route('admin.posts.index'),
            route('admin.posts.index', ['status' => 'draft', 'sort_by' => 'category', 'sort_direction' => 'asc']),
            route('admin.posts.create'),
            route('admin.posts.edit', $post),
            route('admin.categories.index'),
            route('admin.categories.create'),
            route('admin.categories.edit', Category::first()),
            route('admin.users.index'),
            route('admin.users.create'),
            route('admin.users.edit', $admin),
            route('admin.users.edit', User::factory()->create()),
            route('admin.profile.edit'),
        ];

        foreach ($pages as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }

        $this->actingAs($admin)->get(route('admin.posts.index'))
            ->assertSee('Koncept')
            ->assertSee('Naplánováno')
            ->assertSee('Kategorie')
            ->assertSee('Uživatelé');
    }

    public function test_editor_navigation_hides_admin_sections(): void
    {
        $editor = User::factory()->create();

        $this->actingAs($editor)->get(route('admin.posts.index'))
            ->assertOk()
            ->assertSee('Moje příspěvky')
            ->assertDontSee(route('admin.users.index'))
            ->assertDontSee(route('admin.categories.index'));

        $this->actingAs($editor)->get(route('admin.posts.create'))->assertOk();
        $this->actingAs($editor)->get(route('admin.profile.edit'))->assertOk();
    }
}
