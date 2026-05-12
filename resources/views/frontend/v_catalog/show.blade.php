@extends('frontend.v_layouts.app')

@section('content')
<a href="{{ route('frontend.catalog.index') }}" class="btn btn-secondary btn-sm mb-3">Kembali ke Katalog</a>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-5">
                <img
                    src="{{ asset('storage/img-produk/' . $produk->foto) }}"
                    onerror="this.src='{{ asset('storage/img-produk/img-default.jpg') }}'"
                    class="img-fluid rounded mb-3"
                    alt="{{ $produk->nama_produk }}"
                    decoding="async">

                <div id="catalog-gallery" data-gallery-url="{{ $galleryUrl }}">
                    <div class="alert alert-light border mb-0">Galeri tambahan akan dimuat setelah halaman tampil.</div>
                </div>
            </div>
            <div class="col-md-7">
                <h4>{{ $produk->nama_produk }}</h4>
                <p class="text-muted mb-1">Kategori: {{ $produk->kategori->nama_kategori ?? '-' }}</p>
                <p class="text-muted mb-3">Terakhir diperbarui: {{ $produk->updated_at?->format('d-m-Y H:i') }}</p>

                <h5 class="text-primary">Rp. {{ number_format($produk->harga, 0, ',', '.') }}</h5>
                <p>Stok tersedia: <strong>{{ $produk->stok }}</strong></p>
                <hr>
                <div>{!! $produk->detail !!}</div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const galleryContainer = document.getElementById('catalog-gallery');

        if (!galleryContainer) {
            return;
        }

        let galleryLoaded = false;

        async function loadGallery() {
            if (galleryLoaded) {
                return;
            }

            galleryLoaded = true;
            galleryContainer.innerHTML = '<div class="alert alert-light border mb-0">Memuat galeri tambahan...</div>';

            try {
                const response = await fetch(galleryContainer.dataset.galleryUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) {
                    throw new Error('Gagal memuat galeri.');
                }

                galleryContainer.innerHTML = await response.text();
            } catch (error) {
                galleryContainer.innerHTML = '<div class="alert alert-warning mb-0">Galeri tambahan belum dapat dimuat. Silakan muat ulang halaman.</div>';
            }
        }

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries, observerRef) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        loadGallery();
                        observerRef.disconnect();
                    }
                });
            }, { rootMargin: '150px 0px' });

            observer.observe(galleryContainer);
        } else {
            loadGallery();
        }
    });
</script>
@endsection
