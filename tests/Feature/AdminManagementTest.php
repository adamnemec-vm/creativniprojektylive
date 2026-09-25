<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_cannot_access_users_or_categories(): void
    {
        $editor = User::factory()->create();

        $this->actingAs($editor)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($editor)->get(route('admin.categories.index'))->assertForbidden();
        $this->actingAs($editor)->post(route('admin.users.store'), [])->assertForbidden();
    }

    public function test_admin_can_create_editor(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Petr Student',
            'username' => 'petr',
            'email' => 'petr@example.com',
            'role' => 'editor',
            'password' => 'tajne-heslo-123',
            'password_confirmation' => 'tajne-heslo-123',
        ])->assertRedirect(route('admin.users.index'));

        $user = User::where('username', 'petr')->sole();
        $this->assertFalse($user->isAdmin());
        $this->assertTrue(password_verify('tajne-heslo-123', $user->password));
    }

    public function test_admin_can_edit_user_without_changing_password(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->create();
        $hash = $editor->password;

        $this->actingAs($admin)->put(route('admin.users.update', $editor), [
            'name' => 'Nové jméno',
            'username' => $editor->username,
            'email' => $editor->email,
            'role' => 'admin',
        ])->assertSessionHasNoErrors();

        $editor->refresh();
        $this->assertSame('Nové jméno', $editor->name);
        $this->assertTrue($editor->isAdmin());
        $this->assertSame($hash, $editor->password);
    }

    public function test_admin_cannot_change_own_role_or_delete_self(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->admin()->create();

        $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name, 'username' => $admin->username, 'email' => $admin->email, 'role' => 'editor',
        ])->assertSessionHasErrors('role');

        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))->assertSessionHasErrors();

        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_deleting_user_keeps_their_posts(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->create();
        $post = Post::factory()->for($editor, 'author')->create();

        $this->actingAs($admin)->delete(route('admin.users.destroy', $editor));

        $this->assertModelMissing($editor);
        $this->assertNull($post->fresh()->user_id);
    }

    public function test_admin_can_manage_categories(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Animace', 'description' => 'Popis animace',
        ])->assertRedirect(route('admin.categories.index'));

        $category = Category::where('name', 'Animace')->sole();

        $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => 'Animátoři', 'description' => 'Nový popis',
        ])->assertSessionHasNoErrors();
        $this->assertSame('Animátoři', $category->fresh()->name);

        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));
        $this->assertModelMissing($category);
    }

    public function test_category_with_posts_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create();

        $this->actingAs($admin)->delete(route('admin.categories.destroy', $post->category))
            ->assertSessionHasErrors();

        $this->assertModelExists($post->category);
        $this->assertModelExists($post);
    }
}
