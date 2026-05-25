<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\StockLog;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@tokoku.com'],
            [
                'name' => 'Admin TokoKu',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $kasir1 = User::firstOrCreate(
            ['email' => 'kasir1@tokoku.com'],
            [
                'name' => 'Budi Kasir 1',
                'password' => bcrypt('password123'),
                'role' => 'kasir',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $kasir2 = User::firstOrCreate(
            ['email' => 'kasir2@tokoku.com'],
            [
                'name' => 'Siti Kasir 2',
                'password' => bcrypt('password123'),
                'role' => 'kasir',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $pembeli = User::firstOrCreate(
            ['email' => 'pembeli@tokoku.com'],
            [
                'name' => 'Andi Pembeli',
                'password' => bcrypt('password123'),
                'role' => 'pembeli',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Additional pembeli users for more transactions
        $customers = [$pembeli];
        for ($i = 1; $i <= 5; $i++) {
            $customers[] = User::firstOrCreate(
                ['email' => "pembeli{$i}@tokoku.com"],
                [
                    'name' => "Pembeli Ritel {$i}",
                    'password' => bcrypt('password123'),
                    'role' => 'pembeli',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }

        // 2. Seed 10 Categories
        $categoriesData = [
            ['name' => 'Elektronik', 'description' => 'Gadget, laptop, aksesoris, dan perkakas listrik.'],
            ['name' => 'Fashion Pria', 'description' => 'Kaos, kemeja, celana, dan jaket berkualitas.'],
            ['name' => 'Fashion Wanita', 'description' => 'Dress, blouse, rok, dan hijab modern.'],
            ['name' => 'Makanan & Minuman', 'description' => 'Camilan, minuman segar, dan bahan dapur.'],
            ['name' => 'Kecantikan', 'description' => 'Skincare, makeup, dan perawatan tubuh.'],
            ['name' => 'Kesehatan', 'description' => 'Suplemen, vitamin, masker, dan obat-obatan.'],
            ['name' => 'Buku', 'description' => 'Novel, komik, buku pelajaran, dan pengembangan diri.'],
            ['name' => 'Olahraga', 'description' => 'Jersey, perlengkapan gym, sepatu lari, dan botol minum.'],
            ['name' => 'Mainan & Hobi', 'description' => 'Board game, puzzle, mainan edukasi anak.'],
            ['name' => 'Rumah Tangga', 'description' => 'Alat masak, sprei, dekorasi, dan kebersihan.'],
        ];

        $categories = [];
        foreach ($categoriesData as $data) {
            $categories[] = Category::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'is_active' => true,
                    'created_by' => $admin->id,
                ]
            );
        }

        // 3. Seed 30 Products (3 products per category)
        $productsData = [
            // Category 0: Elektronik
            0 => [
                ['name' => 'Keyboard Mechanical RGB', 'price' => 450000, 'weight' => 850],
                ['name' => 'Mouse Wireless Ergonomis', 'price' => 180000, 'weight' => 120],
                ['name' => 'Headset Gaming Bass Boost', 'price' => 350000, 'weight' => 300],
            ],
            // Category 1: Fashion Pria
            1 => [
                ['name' => 'Kaos Polos Cotton Combed 30s', 'price' => 550000, 'weight' => 150],
                ['name' => 'Celana Chino Slimfit Hitam', 'price' => 145000, 'weight' => 400],
                ['name' => 'Jaket Bomber Navy Premium', 'price' => 220000, 'weight' => 600],
            ],
            // Category 2: Fashion Wanita
            2 => [
                ['name' => 'Tunik Katun Adem Motif', 'price' => 115000, 'weight' => 200],
                ['name' => 'Hijab Pashmina Ceruty Baby Doll', 'price' => 35000, 'weight' => 80],
                ['name' => 'Blouse Ruffle Lengan Panjang', 'price' => 95000, 'weight' => 180],
            ],
            // Category 3: Makanan & Minuman
            3 => [
                ['name' => 'Kopi Susu Gula Aren 1L', 'price' => 65000, 'weight' => 1050],
                ['name' => 'Keripik Tempe Renyah Balado', 'price' => 15000, 'weight' => 100],
                ['name' => 'Cokelat Lava Melted Bar', 'price' => 25000, 'weight' => 120],
            ],
            // Category 4: Kecantikan
            4 => [
                ['name' => 'Serum Vitamin C Glow Up', 'price' => 85000, 'weight' => 50],
                ['name' => 'Sunscreen SPF 50 Tone Up', 'price' => 65000, 'weight' => 60],
                ['name' => 'Lip Cream Matte Tahan Lama', 'price' => 45000, 'weight' => 30],
            ],
            // Category 5: Kesehatan
            5 => [
                ['name' => 'Minyak Kayu Putih Asli 120ml', 'price' => 38000, 'weight' => 150],
                ['name' => 'Multivitamin Imunitas 30 Kapsul', 'price' => 110000, 'weight' => 80],
                ['name' => 'Masker Medis 3-Ply 50 Pcs', 'price' => 25000, 'weight' => 200],
            ],
            // Category 6: Buku
            6 => [
                ['name' => 'Novel Kisah Senja di Jakarta', 'price' => 95000, 'weight' => 300],
                ['name' => 'Buku Belajar Cepat PHP 8.4', 'price' => 125000, 'weight' => 350],
                ['name' => 'Komik Detektif Cilik Vol 10', 'price' => 40000, 'weight' => 150],
            ],
            // Category 7: Olahraga
            7 => [
                ['name' => 'Matras Yoga TPE Anti Slip', 'price' => 175000, 'weight' => 1200],
                ['name' => 'Tumble Botol Minum Sport 1L', 'price' => 75000, 'weight' => 250],
                ['name' => 'Tali Skipping Counter Digital', 'price' => 55000, 'weight' => 180],
            ],
            // Category 8: Mainan & Hobi
            8 => [
                ['name' => 'Puzzle Kayu Peta Indonesia', 'price' => 90000, 'weight' => 450],
                ['name' => 'Rubik 3x3 Speed Cube Smooth', 'price' => 45000, 'weight' => 90],
                ['name' => 'Kartu Board Game Nusantara', 'price' => 120000, 'weight' => 300],
            ],
            // Category 9: Rumah Tangga
            9 => [
                ['name' => 'Wajan Teflon Anti Lengket 24cm', 'price' => 135000, 'weight' => 800],
                ['name' => 'Pisau Dapur Stainless Set 5 in 1', 'price' => 85000, 'weight' => 600],
                ['name' => 'Lampu Meja Belajar Led Sentuh', 'price' => 60000, 'weight' => 400],
            ],
        ];

        $products = [];
        foreach ($categories as $catIndex => $category) {
            $prodList = $productsData[$catIndex];
            foreach ($prodList as $pData) {
                $products[] = Product::firstOrCreate(
                    ['slug' => Str::slug($pData['name'])],
                    [
                        'category_id' => $category->id,
                        'name' => $pData['name'],
                        'description' => "Ini adalah deskripsi produk premium {$pData['name']}. Sangat direkomendasikan untuk menunjang aktivitas Anda sehari-hari.",
                        'price_sell' => $pData['price'],
                        'weight' => $pData['weight'],
                        'is_active' => true,
                        'created_by' => $admin->id,
                    ]
                );
            }
        }

        // 4. Seed Stock Batches for each product (3-5 FIFO batches)
        // We will seed 3 batches per product with different purchase price, date and initial stock
        foreach ($products as $product) {
            // Batch 1 (Oldest, e.g. 10 days ago)
            $qty1 = rand(15, 25);
            $batch1 = StockBatch::create([
                'product_id' => $product->id,
                'quantity_initial' => $qty1,
                'quantity_remaining' => $qty1,
                'purchase_price' => round($product->price_sell * 0.70, -3),
                'batch_date' => Carbon::now()->subDays(10),
                'supplier_name' => 'PT. Global Supplier Nusantara',
                'note' => 'Stok Awal Batch A',
                'created_by' => $admin->id,
            ]);

            StockLog::create([
                'product_id' => $product->id,
                'stock_batch_id' => $batch1->id,
                'type' => 'in',
                'quantity' => $qty1,
                'description' => 'Pemasukan stok awal Batch A',
                'created_by' => $admin->id,
            ]);

            // Batch 2 (Mid, e.g. 5 days ago)
            $qty2 = rand(25, 35);
            $batch2 = StockBatch::create([
                'product_id' => $product->id,
                'quantity_initial' => $qty2,
                'quantity_remaining' => $qty2,
                'purchase_price' => round($product->price_sell * 0.75, -3),
                'batch_date' => Carbon::now()->subDays(5),
                'supplier_name' => 'CV. Jaya Distribusi Utama',
                'note' => 'Stok Tambahan Batch B',
                'created_by' => $admin->id,
            ]);

            StockLog::create([
                'product_id' => $product->id,
                'stock_batch_id' => $batch2->id,
                'type' => 'in',
                'quantity' => $qty2,
                'description' => 'Pemasukan stok tambahan Batch B',
                'created_by' => $admin->id,
            ]);

            // Batch 3 (Newest, e.g. 1 day ago)
            $qty3 = rand(30, 45);
            $batch3 = StockBatch::create([
                'product_id' => $product->id,
                'quantity_initial' => $qty3,
                'quantity_remaining' => $qty3,
                'purchase_price' => round($product->price_sell * 0.78, -3),
                'batch_date' => Carbon::now()->subDays(1),
                'supplier_name' => 'PT. Prima Ritel Sinergi',
                'note' => 'Restock Rutin Batch C',
                'created_by' => $admin->id,
            ]);

            StockLog::create([
                'product_id' => $product->id,
                'stock_batch_id' => $batch3->id,
                'type' => 'in',
                'quantity' => $qty3,
                'description' => 'Pemasukan stok tambahan Batch C',
                'created_by' => $admin->id,
            ]);
        }

        // 5. Seed 20 Sample Orders simulating FIFO stock deduction
        // Let's create transactions spread across the last 4 days
        $paymentMethods = ['Tunai', 'Transfer Bank', 'Qris'];
        $cashiers = [$kasir1, $kasir2, null]; // null means self checkout online

        for ($orderIndex = 1; $orderIndex <= 20; $orderIndex++) {
            $customer = $customers[array_rand($customers)];
            $cashier = $cashiers[array_rand($cashiers)];
            $orderDate = Carbon::now()->subHours(rand(1, 96));

            $orderNumber = 'ORD-' . $orderDate->format('Ymd') . '-' . str_pad($orderIndex, 4, '0', STR_PAD_LEFT);
            
            // Choose 1-3 random products to purchase
            $randomProducts = array_values(collect($products)->random(rand(1, 3))->all());
            
            $subtotal = 0;
            $itemsToCreate = [];

            foreach ($randomProducts as $product) {
                $qtyToBuy = rand(1, 3);
                
                // FIFO logic simulation
                $batches = StockBatch::where('product_id', $product->id)
                    ->where('quantity_remaining', '>', 0)
                    ->orderBy('batch_date', 'asc')
                    ->get();
                
                $allocatedQty = 0;
                $remToAllocate = $qtyToBuy;

                foreach ($batches as $batch) {
                    if ($remToAllocate <= 0) break;

                    $take = min($batch->quantity_remaining, $remToAllocate);
                    
                    // Deduct from batch
                    $batch->quantity_remaining -= $take;
                    $batch->save();

                    // Track allocations for order items
                    $itemsToCreate[] = [
                        'product_id' => $product->id,
                        'stock_batch_id' => $batch->id,
                        'product_name' => $product->name,
                        'price' => $product->price_sell,
                        'quantity' => $take,
                        'subtotal' => $product->price_sell * $take,
                    ];

                    $subtotal += $product->price_sell * $take;
                    $allocatedQty += $take;
                    $remToAllocate -= $take;
                }
            }

            if (empty($itemsToCreate)) {
                // If somehow no stock available, skip
                continue;
            }

            // Calculation
            $taxAmount = $subtotal * 0.11; // 11% Tax
            $discountAmount = $orderIndex % 5 === 0 ? 10000 : 0; // Discount 10k every 5th order
            $total = $subtotal + $taxAmount - $discountAmount;

            $status = $orderIndex === 20 ? 'pending' : 'paid'; // Make 1 pending, rest paid

            $order = Order::create([
                'user_id' => $customer->id,
                'kasir_id' => $cashier ? $cashier->id : null,
                'order_number' => $orderNumber,
                'status' => $status,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_proof' => $status === 'paid' ? 'proof_demo.jpg' : null,
                'note' => 'Transaksi sample seeder.',
                'paid_at' => $status === 'paid' ? $orderDate : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            foreach ($itemsToCreate as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);

                // Create stock log for 'out'
                StockLog::create([
                    'product_id' => $itemData['product_id'],
                    'stock_batch_id' => $itemData['stock_batch_id'],
                    'type' => 'out',
                    'quantity' => $itemData['quantity'],
                    'description' => "Pengurangan stok penjualan {$order->order_number}",
                    'reference_id' => $order->id,
                    'created_by' => $cashier ? $cashier->id : $customer->id,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);
            }
        }
    }
}
