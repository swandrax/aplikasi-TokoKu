@extends('frontend.v_layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">{{ $judul }}</h4>
        <small class="text-muted">Sinkron terakhir: <span id="last-sync">{{ $serverTime }}</span></small>
    </div>
    <div>
        <span class="badge badge-primary p-2">Total produk aktif: <span id="total-produk-aktif">...</span></span>
    </div>
</div>

<div class="row" id="catalog-grid">
    @forelse ($produk as $item)
    <div class="col-md-3 mb-4">
        <div class="card card-product h-100">
            <img
                src="{{ asset('storage/img-produk/' . $item->foto) }}"
                onerror="this.src='{{ asset('storage/img-produk/img-default.jpg') }}'"
                class="card-img-top product-image"
                alt="{{ $item->nama_produk }}"
                loading="lazy"
                decoding="async">
            <div class="card-body d-flex flex-column">
                <p class="mb-1 text-muted">{{ $item->kategori?->nama_kategori ?? '-' }}</p>
                <h6 class="card-title">{{ $item->nama_produk }}</h6>
                <p class="card-text text-muted mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($item->detail), 70) }}</p>
                <p class="mb-1"><strong id="harga-produk-{{ $item->id }}">Rp. {{ number_format($item->harga, 0, ',', '.') }}</strong></p>
                <p class="mb-2">Stok: <span id="stok-produk-{{ $item->id }}">{{ $item->stok }}</span></p>
                <a href="{{ route('frontend.catalog.show', $item->id) }}" class="btn btn-outline-primary btn-sm mt-auto">Lihat Detail</a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-warning">Belum ada produk aktif untuk ditampilkan.</div>
    </div>
    @endforelse
</div>

<div class="d-flex justify-content-center">
    {{ $produk->links() }}
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const realtimeUrl = "{{ route('frontend.catalog.realtime') }}";
        const totalProdukElement = document.getElementById('total-produk-aktif');
        const lastSyncElement = document.getElementById('last-sync');
        let lastSync = "{{ $serverTime }}";

        function toRupiah(value) {
            return 'Rp. ' + Number(value).toLocaleString('id-ID');
        }

        async function syncCatalog() {
            try {
                const response = await fetch(`${realtimeUrl}?last_sync=${encodeURIComponent(lastSync)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) {
                    return;
                }

                const result = await response.json();
                lastSync = result.server_time || lastSync;
                if (lastSyncElement) {
                    lastSyncElement.textContent = result.server_time || '-';
                }
                if (totalProdukElement) {
                    totalProdukElement.textContent = result.total_produk_aktif ?? totalProdukElement.textContent;
                }

                (result.data || []).forEach(function(item) {
                    const stokElement = document.getElementById(`stok-produk-${item.id}`);
                    const hargaElement = document.getElementById(`harga-produk-${item.id}`);
                    if (stokElement) {
                        stokElement.textContent = item.stok;
                    }
                    if (hargaElement) {
                        hargaElement.textContent = toRupiah(item.harga);
                    }
                });
            } catch (error) {
                console.error('Sinkronisasi realtime gagal:', error);
            }
        }

        function scheduleInitialSync() {
            if ('requestIdleCallback' in window) {
                window.requestIdleCallback(syncCatalog);
                return;
            }

            setTimeout(syncCatalog, 0);
        }

        scheduleInitialSync();

        setInterval(function() {
            if (document.visibilityState === 'visible') {
                syncCatalog();
            }
        }, 10000);
    });
</script>
@endsection
