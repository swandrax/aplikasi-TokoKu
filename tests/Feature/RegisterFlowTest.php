<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_register_page(): void
    {
        $response = $this->get(route('backend.register'));

        $response->assertOk();
    }

    public function test_register_customer_then_login_redirects_to_catalog(): void
    {
        $registerResponse = $this->post(route('backend.register.store'), [
            'nama' => 'Customer Baru',
            'email' => 'customerbaru@example.com',
            'role' => '2',
            'hp' => '081234567890',
            'password' => 'P@55word',
            'password_confirmation' => 'P@55word',
        ]);

        $registerResponse->assertRedirect(route('backend.login'));
        $this->assertDatabaseHas('user', [
            'email' => 'customerbaru@example.com',
            'role' => '2',
            'status' => 1,
        ]);

        $loginResponse = $this->post(route('backend.login.authenticate'), [
            'email' => 'customerbaru@example.com',
            'password' => 'P@55word',
        ]);

        $loginResponse->assertRedirect(route('frontend.catalog.index'));
    }

    public function test_register_admin_then_login_redirects_to_backend_dashboard(): void
    {
        $registerResponse = $this->post(route('backend.register.store'), [
            'nama' => 'Admin Baru',
            'email' => 'adminbaru@example.com',
            'role' => '1',
            'hp' => '081234567891',
            'password' => 'P@55word',
            'password_confirmation' => 'P@55word',
        ]);

        $registerResponse->assertRedirect(route('backend.login'));
        $this->assertDatabaseHas('user', [
            'email' => 'adminbaru@example.com',
            'role' => '1',
            'status' => 1,
        ]);

        $loginResponse = $this->post(route('backend.login.authenticate'), [
            'email' => 'adminbaru@example.com',
            'password' => 'P@55word',
        ]);

        $loginResponse->assertRedirect(route('backend.beranda'));
        $this->assertAuthenticated();
        $this->assertEquals('1', User::where('email', 'adminbaru@example.com')->firstOrFail()->role);
    }
}
