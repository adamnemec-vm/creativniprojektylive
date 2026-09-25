<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_lists_only_published_posts(): void
    {
        Post::factory()->create(['title' => 'Veřejný příspěvek']);
        Post::factory()->draft()->create(['title' => 'Tajný koncept']);
        Post::factory()->scheduled()->create(['title' => 'Budoucí příspěvek']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Veřejný příspěvek')
            ->assertDontSee('Tajný koncept')
            ->assertDontSee('Budoucí příspěvek');
    }

    public function test_excerpt_does_not_render_html_from_content(): void
    {
        Post::factory()->create(['content' => '<p>&lt;img src=x onerror=alert(1)&gt;</p>']);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('<img src=x onerror=alert(1)>', false)
            ->assertSee('&lt;img src=x onerror=alert(1)&gt;', false);
    }

    public function test_search_filters_posts(): void
    {
        Post::factory()->create(['title' => 'Natáčení s dronem']);
        Post::factory()->create(['title' => 'Typografie v praxi']);

        $this->get('/?q=dronem')
            ->assertOk()
            ->assertSee('Natáčení s dronem')
            ->assertDontSee('Typografie v praxi');
    }

    public function test_category_page_lists_only_its_published_posts(): void
    {
        $category = Category::factory()->create(['name' => 'Filmaři']);
        Post::factory()->for($category)->create(['title' => 'Film A']);
        Post::factory()->for($category)->draft()->create(['title' => 'Film koncept']);
        Post::factory()->create(['title' => 'Jiná kategorie']);

        $this->get(route('categories.show', $category))
            ->assertOk()
            ->assertSee('Film A')
            ->assertDontSee('Film koncept')
            ->assertDontSee('Jiná kategorie');
    }

    public function test_published_post_has_seo_meta(): void
    {
        $post = Post::factory()->create(['title' => 'Dron nad Prahou']);

        $this->get(route('posts.show', $post))
            ->assertOk()
            ->assertSee('<title>Dron nad Prahou | Projekty CHC </title>', false)
            ->assertSee('<meta property="og:type" content="article">', false);
    }

    public function test_draft_is_hidden_from_guests_but_visible_to_author_and_admin(): void
    {
        $author = User::factory()->create();
        $post = Post::factory()->draft()->for($author, 'author')->create();

        $this->get(route('posts.show', $post))->assertNotFound();
        $this->actingAs(User::factory()->create())->get(route('posts.show', $post))->assertNotFound();
        $this->actingAs($author)->get(route('posts.show', $post))->assertOk()->assertSee('Náhled');
        $this->actingAs(User::factory()->admin()->create())->get(route('posts.show', $post))->assertOk();
    }
}
