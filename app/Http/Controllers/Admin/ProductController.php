<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category', 'creator'])->orderBy('name', 'asc')->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::query()->where('is_active', true)->orderBy('name', 'asc')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|min:3|max:100',
            'description' => 'nullable|string|max:2000',
            'price_sell' => 'required|numeric|min:0|max:999999999999',
            'weight' => 'required|numeric|min:0|max:999999',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'name.required' => 'Nama produk wajib diisi.',
            'name.min' => 'Nama produk minimal 3 karakter.',
            'price_sell.required' => 'Harga jual wajib diisi.',
            'price_sell.numeric' => 'Harga jual harus berupa angka.',
            'price_sell.min' => 'Harga jual tidak boleh negatif.',
            'weight.required' => 'Berat produk wajib diisi.',
            'weight.numeric' => 'Berat produk harus berupa angka.',
            'image.image' => 'File yang diupload harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $slug = Str::slug($request->name);

        // Enforce unique slug
        if (Product::query()->where('slug', $slug)->exists()) {
            return back()->withInput()->withErrors(['name' => 'Nama produk sudah terpakai (menghasilkan slug duplikat).']);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'price_sell' => $request->price_sell,
            'weight' => $request->weight,
            'image' => $imagePath,
            'is_active' => $request->has('is_active'),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Produk baru berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::query()->where('is_active', true)->orderBy('name', 'asc')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|min:3|max:100',
            'description' => 'nullable|string|max:2000',
            'price_sell' => 'required|numeric|min:0|max:999999999999',
            'weight' => 'required|numeric|min:0|max:999999',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'price_sell.required' => 'Harga jual wajib diisi.',
            'weight.required' => 'Berat produk wajib diisi.',
        ]);

        $slug = Str::slug($request->name);

        if (Product::query()->where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
            return back()->withInput()->withErrors(['name' => 'Nama produk ini menghasilkan slug yang bertabrakan dengan produk lain.']);
        }

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'price_sell' => $request->price_sell,
            'weight' => $request->weight,
            'image' => $imagePath,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        Product::destroy($product->id);
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dinonaktifkan (Soft Deleted).');
    }
}
