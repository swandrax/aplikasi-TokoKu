<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'status' => 1,
        ]);
    }

    public function test_admin_can_access_backend_user_management(): void
    {
        $admin = $this->makeUser('1');

        $response = $this->actingAs($admin)->get(route('backend.user.index'));

        $response->assertOk();
    }

    public function test_user_admin_cannot_access_user_index_but_can_access_produk(): void
    {
        $userAdmin = $this->makeUser('0');

        $userResponse = $this->actingAs($userAdmin)->get(route('backend.user.index'));
        $produkResponse = $this->actingAs($userAdmin)->get(route('backend.produk.index'));

        $userResponse->assertForbidden();
        $produkResponse->assertOk();
    }

    public function test_customer_can_access_frontend_catalog_but_not_backend(): void
    {
        $customer = $this->makeUser('2');

        $frontendResponse = $this->actingAs($customer)->get(route('frontend.catalog.index'));
        $backendResponse = $this->actingAs($customer)->get(route('backend.beranda'));

        $frontendResponse->assertOk();
        $backendResponse->assertForbidden();
    }
}
