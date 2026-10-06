<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_trying_to_access_protected_route(): void
    {
        $response = $this->get(route('books.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_login_and_access_category_management(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@pens.ac.id',
            'role' => 'admin',
            'password' => 'password',
        ]);

        $loginResponse = $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $loginResponse->assertRedirect(route('books.index'))
            ->assertSessionHas('success', 'Login berhasil. Selamat datang!');

        $this->assertAuthenticatedAs($admin);

        $this->get(route('categories.index'))->assertOk();
    }

    public function test_petugas_cannot_access_admin_category_route(): void
    {
        $petugas = User::factory()->create([
            'role' => 'petugas',
        ]);

        $response = $this->actingAs($petugas)->get(route('categories.index'));

        $response->assertStatus(403);
    }

    public function test_user_can_change_password_from_profile_page(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
            'role' => 'petugas',
        ]);

        $response = $this->actingAs($user)->post(route('profile.updatePassword'), [
            'current_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Password berhasil diperbarui.');

        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    }
}
