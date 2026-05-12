<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CatalogController extends Controller
{
    public function index()
    {
        $produk = Produk::query()
            ->select(['id', 'kategori_id', 'nama_produk', 'detail', 'harga', 'stok', 'foto', 'updated_at'])
            ->with(['kategori:id,nama_kategori'])
            ->where('status', 1)
            ->orderByDesc('updated_at')
            ->simplePaginate(8);

        return view('frontend.v_catalog.index', [
            'judul' => 'Katalog Produk',
            'produk' => $produk,
            'serverTime' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function show(Produk $produk)
    {
        abort_if((int) $produk->status !== 1, 404);

        $produk->loadMissing('kategori:id,nama_kategori');

        return view('frontend.v_catalog.show', [
            'judul' => 'Detail Produk',
            'produk' => $produk,
            'galleryUrl' => route('frontend.catalog.gallery', $produk),
        ]);
    }

    public function gallery(Produk $produk)
    {
        abort_if((int) $produk->status !== 1, 404);

        $fotoProduk = $produk->fotoProduk()
            ->select(['id', 'produk_id', 'foto'])
            ->latest('id')
            ->get();

        return view('frontend.v_catalog.partials.gallery', [
            'produk' => $produk,
            'fotoProduk' => $fotoProduk,
        ]);
    }

    public function realtimeProduk(Request $request): JsonResponse
    {
        $lastSync = $request->query('last_sync');
        $query = Produk::query()
            ->select(['id', 'kategori_id', 'nama_produk', 'harga', 'stok', 'updated_at'])
            ->with(['kategori:id,nama_kategori'])
            ->where('status', 1);

        if ($lastSync) {
            try {
                $time = Carbon::parse($lastSync);
                $query->where('updated_at', '>', $time);
            } catch (\Exception) {
                // Abaikan format tanggal invalid, kirim data penuh.
            }
        }

        $produk = $query
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get()
            ->map(function (Produk $item) {
                return [
                    'id' => $item->id,
                    'nama_produk' => $item->nama_produk,
                    'kategori' => $item->kategori?->nama_kategori ?? '-',
                    'harga' => (float) $item->harga,
                    'stok' => $item->stok,
                    'updated_at' => $item->updated_at?->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'server_time' => now()->format('Y-m-d H:i:s'),
            'total_produk_aktif' => Produk::where('status', 1)->count(),
            'data' => $produk,
        ]);
    }
}
