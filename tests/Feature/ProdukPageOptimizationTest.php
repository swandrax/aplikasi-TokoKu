<?php

namespace Tests\Feature;

use App\Models\FotoProduk;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProdukPageOptimizationTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'status' => 1,
        ]);
    }

    private function makeProduk(array $overrides = []): Produk
    {
        $kategori = Kategori::create([
            'nama_kategori' => 'Kategori Uji ' . uniqid(),
        ]);

        $user = $this->makeUser('1');

        return Produk::create(array_merge([
            'kategori_id' => $kategori->id,
            'user_id' => $user->id,
            'status' => 1,
            'nama_produk' => 'Produk Uji ' . uniqid(),
            'detail' => '<p>Detail produk uji</p>',
            'harga' => 15000,
            'stok' => 10,
            'berat' => 250,
            'foto' => 'utama.jpg',
        ], $overrides));
    }

    public function test_backend_product_detail_loads_gallery_lazily(): void
    {
        $admin = $this->makeUser('1');
        $produk = $this->makeProduk();

        FotoProduk::create([
            'produk_id' => $produk->id,
            'foto' => 'galeri-admin.jpg',
        ]);

        $response = $this->actingAs($admin)->get(route('backend.produk.show', $produk));

        $response->assertOk();
        $response->assertSee('Galeri foto akan dimuat setelah halaman tampil.');
        $response->assertDontSee('galeri-admin.jpg');

        $galleryResponse = $this->actingAs($admin)->get(route('backend.produk.gallery', $produk));

        $galleryResponse->assertOk();
        $galleryResponse->assertSee('galeri-admin.jpg');
    }

    public function test_frontend_catalog_detail_loads_gallery_lazily(): void
    {
        $customer = $this->makeUser('2');
        $produk = $this->makeProduk();

        FotoProduk::create([
            'produk_id' => $produk->id,
            'foto' => 'galeri-customer.jpg',
        ]);

        $response = $this->actingAs($customer)->get(route('frontend.catalog.show', $produk));

        $response->assertOk();
        $response->assertSee('Galeri tambahan akan dimuat setelah halaman tampil.');
        $response->assertDontSee('galeri-customer.jpg');

        $galleryResponse = $this->actingAs($customer)->get(route('frontend.catalog.gallery', $produk));

        $galleryResponse->assertOk();
        $galleryResponse->assertSee('galeri-customer.jpg');
    }

    public function test_product_store_uses_stricter_numeric_validation(): void
    {
        $admin = $this->makeUser('1');
        $kategori = Kategori::create([
            'nama_kategori' => 'Snack Nusantara',
        ]);

        $response = $this->actingAs($admin)->from(route('backend.produk.create'))->post(route('backend.produk.store'), [
            'kategori_id' => $kategori->id,
            'nama_produk' => 'Produk Validasi',
            'detail' => 'Deskripsi produk',
            'harga' => -1000,
            'berat' => 'bukan-angka',
            'stok' => -3,
            'foto' => UploadedFile::fake()->image('produk.jpg'),
        ]);

        $response->assertRedirect(route('backend.produk.create'));
        $response->assertSessionHasErrors(['harga', 'berat', 'stok']);
    }
}
