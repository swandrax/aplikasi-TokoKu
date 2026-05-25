<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockBatch;
use App\Services\SearchService;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test landing page redirects guest to login.
     */
    public function test_landing_page_redirects_guest_to_login(): void
    {
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test login page loads successfully.
     */
    public function test_login_page_loads_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('TokoKu');
        $response->assertSee('Alamat Email');
    }

    /**
     * Test register page loads successfully.
     */
    public function test_register_page_loads_successfully(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Baru');
    }

    /**
     * Test Boyer-Moore search string matching.
     */
    public function test_boyer_moore_search_matching(): void
    {
        $searchService = new SearchService();
        
        $text = "Keyboard Mechanical RGB Premium";
        $pattern = "Mechanical";

        $matches = $searchService->boyerMoore($text, $pattern);
        
        $this->assertNotEmpty($matches);
        $this->assertEquals(9, $matches[0]); // "Mechanical" starts at index 9
    }

    /**
     * Test FIFO stock deduction service logic.
     */
    public function test_fifo_stock_deduction(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admintest@tokoku.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
            'created_by' => $admin->id,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price_sell' => 100000,
            'weight' => 500,
            'created_by' => $admin->id,
        ]);

        // Create 2 FIFO batches
        // Batch 1 (Older): 10 units, price 70k
        $batch1 = StockBatch::create([
            'product_id' => $product->id,
            'quantity_initial' => 10,
            'quantity_remaining' => 10,
            'purchase_price' => 70000,
            'batch_date' => now()->subDays(5),
            'created_by' => $admin->id,
        ]);

        // Batch 2 (Newer): 15 units, price 80k
        $batch2 = StockBatch::create([
            'product_id' => $product->id,
            'quantity_initial' => 15,
            'quantity_remaining' => 15,
            'purchase_price' => 80000,
            'batch_date' => now()->subDays(1),
            'created_by' => $admin->id,
        ]);

        $stockService = new StockService();
        
        // Deduct 12 units (should consume 10 units from Batch 1 and 2 units from Batch 2)
        $result = $stockService->deductStock($product->id, 12, 999);

        $this->assertTrue($result['success']);
        
        // Reload batches from DB
        $batch1->refresh();
        $batch2->refresh();

        $this->assertEquals(0, $batch1->quantity_remaining); // Consumed fully
        $this->assertEquals(13, $batch2->quantity_remaining); // 15 - 2 = 13 remaining
    }
}
