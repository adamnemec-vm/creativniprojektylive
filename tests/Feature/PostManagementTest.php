<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostManagementTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Můj projekt',
            'content' => '<p>Obsah</p>',
            'category_id' => Category::factory()->create()->id,
            'status' => 'published',
        ], $overrides);
    }

    public function test_editor_can_create_post_and_becomes_its_author(): void
    {
        $editor = User::factory()->create();

        $this->actingAs($editor)->post(route('admin.posts.store'), $this->payload())
            ->assertRedirect(route('admin.posts.index'));

        $post = Post::sole();
        $this->assertSame($editor->id, $post->user_id);
        $this->assertSame('muj-projekt', $post->slug);
        $this->assertTrue($post->isPublished());
    }

    public function test_duplicate_titles_get_unique_slugs(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.posts.store'), $this->payload())->assertSessionHasNoErrors();
        $this->actingAs($admin)->post(route('admin.posts.store'), $this->payload())->assertSessionHasNoErrors();

        $this->assertEquals(['muj-projekt', 'muj-projekt-2'], Post::orderBy('id')->pluck('slug')->all());
    }

    public function test_slug_stays_when_title_changes(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create(['title' => 'Původní', 'slug' => 'puvodni']);

        $this->actingAs($admin)->put(route('admin.posts.update', $post), $this->payload(['title' => 'Nový název']))
            ->assertSessionHasNoErrors();

        $this->assertSame('puvodni', $post->fresh()->slug);
        $this->assertSame('Nový název', $post->fresh()->title);
    }

    public function test_slug_can_be_changed_explicitly_but_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        Post::factory()->create(['slug' => 'obsazeno']);
        $post = Post::factory()->create(['slug' => 'puvodni']);

        $this->actingAs($admin)->put(route('admin.posts.update', $post), $this->payload(['slug' => 'obsazeno']))
            ->assertSessionHasErrors('slug');

        $this->actingAs($admin)->put(route('admin.posts.update', $post), $this->payload(['slug' => 'nova-adresa']))
            ->assertSessionHasNoErrors();

        $this->assertSame('nova-adresa', $post->fresh()->slug);
    }

    public function test_draft_and_scheduled_publishing(): void
    {
        $editor = User::factory()->create();

        $this->actingAs($editor)->post(route('admin.posts.store'), $this->payload(['title' => 'Koncept', 'status' => 'draft']));
        $this->actingAs($editor)->post(route('admin.posts.store'), $this->payload([
            'title' => 'Plán', 'published_at' => now()->addDays(3)->format('Y-m-d\TH:i'),
        ]));

        $this->assertNull(Post::where('title', 'Koncept')->sole()->published_at);
        $this->assertTrue(Post::where('title', 'Plán')->sole()->isScheduled());
    }

    public function test_content_is_sanitized(): void
    {
        $editor = User::factory()->create();

        $this->actingAs($editor)->post(route('admin.posts.store'), $this->payload([
            'content' => '<p onclick="steal()">Text<script>alert(1)</script></p>'
                .'<iframe src="https://www.youtube.com/embed/abc"></iframe>'
                .'<iframe src="https://evil.example/x"></iframe>',
        ]));

        $content = Post::sole()->content;
        $this->assertStringNotContainsString('<script', $content);
        $this->assertStringNotContainsString('onclick', $content);
        $this->assertStringNotContainsString('evil.example', $content);
        $this->assertStringContainsString('youtube.com/embed/abc', $content);
        $this->assertStringContainsString('<p>Text</p>', $content);
    }

    public function test_editor_sees_and_edits_only_own_posts(): void
    {
        $editor = User::factory()->create();
        $own = Post::factory()->for($editor, 'author')->create(['title' => 'Můj článek']);
        $foreign = Post::factory()->create(['title' => 'Cizí článek']);

        $this->actingAs($editor)->get(route('admin.posts.index'))
            ->assertOk()
            ->assertSee('Můj článek')
            ->assertDontSee('Cizí článek');

        $this->actingAs($editor)->get(route('admin.posts.edit', $own))->assertOk();
        $this->actingAs($editor)->get(route('admin.posts.edit', $foreign))->assertForbidden();
        $this->actingAs($editor)->put(route('admin.posts.update', $foreign), $this->payload())->assertForbidden();
        $this->actingAs($editor)->delete(route('admin.posts.destroy', $foreign))->assertForbidden();
        $this->assertModelExists($foreign);
    }

    public function test_admin_can_edit_and_delete_any_post(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create();

        $this->actingAs($admin)->get(route('admin.posts.edit', $post))->assertOk();
        $this->actingAs($admin)->delete(route('admin.posts.destroy', $post))->assertRedirect();
        $this->assertModelMissing($post);
    }

    public function test_uploaded_images_are_resized_and_converted_to_webp(): void
    {
        Storage::fake('public');
        $editor = User::factory()->create();

        $this->actingAs($editor)->post(route('admin.posts.store'), $this->payload([
            'thumbnail' => UploadedFile::fake()->image('nahled.jpg', 3000, 2000),
            'images' => [
                UploadedFile::fake()->image('a.jpg', 4000, 1000),
                UploadedFile::fake()->image('b.png', 800, 600),
            ],
        ]))->assertSessionHasNoErrors();

        $post = Post::sole();
        $this->assertStringEndsWith('.webp', $post->thumbnail_path);
        Storage::disk('public')->assertExists($post->thumbnail_path);

        [$width] = getimagesizefromstring(Storage::disk('public')->get($post->thumbnail_path));
        $this->assertSame(1200, $width);

        $images = $post->images;
        $this->assertCount(2, $images);
        [$width] = getimagesizefromstring(Storage::disk('public')->get($images[0]->image_path));
        $this->assertSame(1920, $width);
        [$width] = getimagesizefromstring(Storage::disk('public')->get($images[1]->image_path));
        $this->assertSame(800, $width, 'Menší obrázky se nezvětšují.');
    }

    public function test_gallery_order_and_alt_texts_can_be_updated(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create();
        $first = $post->images()->create(['image_path' => 'posts/1.webp', 'sort_order' => 1]);
        $second = $post->images()->create(['image_path' => 'posts/2.webp', 'sort_order' => 2]);

        $this->actingAs($admin)->put(route('admin.posts.update', $post), $this->payload([
            'image_order' => [$second->id, $first->id],
            'image_alt' => [$first->id => 'Záběr z natáčení', $second->id => ''],
        ]))->assertSessionHasNoErrors();

        $this->assertEquals([$second->id, $first->id], $post->images()->pluck('id')->all());
        $this->assertSame('Záběr z natáčení', $first->fresh()->alt);
        $this->assertNull($second->fresh()->alt);
    }

    public function test_deleting_post_removes_its_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('posts/thumbnails/t.webp', 'x');
        Storage::disk('public')->put('posts/g.webp', 'x');

        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create(['thumbnail_path' => 'posts/thumbnails/t.webp']);
        $post->images()->create(['image_path' => 'posts/g.webp']);

        $this->actingAs($admin)->delete(route('admin.posts.destroy', $post));

        Storage::disk('public')->assertMissing('posts/thumbnails/t.webp');
        Storage::disk('public')->assertMissing('posts/g.webp');
    }

    public function test_editor_cannot_delete_image_of_foreign_post(): void
    {
        $editor = User::factory()->create();
        $image = Post::factory()->create()->images()->create(['image_path' => 'posts/x.webp']);

        $this->actingAs($editor)->deleteJson(route('admin.images.destroy', $image))->assertForbidden();
        $this->assertModelExists($image);
    }
}
