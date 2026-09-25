<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in_with_username(): void
    {
        $user = User::factory()->create(['username' => 'jana']);

        $this->post('/login', ['username' => 'jana', 'password' => 'password'])
            ->assertRedirect(route('admin.posts.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_is_throttled_after_five_failed_attempts(): void
    {
        User::factory()->create(['username' => 'jana']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['username' => 'jana', 'password' => 'spatne']);
        }

        // Ani správné heslo teď neprojde.
        $this->post('/login', ['username' => 'jana', 'password' => 'password'])
            ->assertSessionHasErrors('username');

        $this->assertStringStartsWith('Příliš mnoho pokusů', session('errors')->first('username'));
        $this->assertGuest();
    }

    public function test_admin_area_requires_login(): void
    {
        $this->get(route('admin.posts.index'))->assertRedirect(route('login'));
    }

    public function test_migrations_do_not_create_default_admin_account(): void
    {
        $this->assertDatabaseMissing('users', ['username' => 'admin']);
    }

    public function test_create_admin_command_creates_administrator(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Jméno', 'Hlavní Admin')
            ->expectsQuestion('Uživatelské jméno', 'spravce')
            ->expectsQuestion('E-mail', 'spravce@example.com')
            ->expectsQuestion('Heslo', 'Silne-heslo-2026')
            ->assertSuccessful();

        $user = User::where('username', 'spravce')->sole();
        $this->assertTrue($user->isAdmin());
        $this->assertTrue(password_verify('Silne-heslo-2026', $user->password));
    }
}
