<?php
namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class BerandaController extends Controller
{
    public function berandaBackend()
    {
        return view('backend.v_beranda.index', [
            'judul' => 'Halaman Beranda',
            'summary' => [],
        ]);
    }

    public function summaryRealtime(): JsonResponse
    {
        return response()->json($this->summaryData());
    }

    private function summaryData(): array
    {
        return [
            'total_user' => User::count(),
            'total_kategori' => Kategori::count(),
            'total_produk' => Produk::count(),
            'total_produk_aktif' => Produk::where('status', 1)->count(),
            'produk_terbaru' => Produk::query()
                ->select(['id', 'kategori_id', 'nama_produk', 'stok', 'harga', 'updated_at'])
                ->with(['kategori:id,nama_kategori'])
                ->orderByDesc('updated_at')
                ->limit(5)
                ->get()
                ->map(function (Produk $produk) {
                    return [
                        'id' => $produk->id,
                        'nama_produk' => $produk->nama_produk,
                        'kategori' => $produk->kategori?->nama_kategori ?? '-',
                        'stok' => $produk->stok,
                        'harga' => (float) $produk->harga,
                        'updated_at' => $produk->updated_at?->format('Y-m-d H:i:s'),
                    ];
                }),
            'server_time' => now()->format('Y-m-d H:i:s'),
        ];
    }
}
