@extends('backend.v_layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ $judul }}</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="kategori_id" class="form-control" disabled>
                                    <option value="" selected>- Pilih Kategori -</option>
                                    @foreach ($kategori as $row)
                                    <option value="{{ $row->id }}" {{ $show->kategori_id == $row->id ? 'selected' : '' }}>{{ $row->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nama Produk</label>
                                <input type="text" value="{{ $show->nama_produk }}" class="form-control" disabled>
                            </div>
                            <div class="form-group">
                                <label>Detail</label>
                                <div class="form-control" style="min-height: 180px; overflow-y: auto;">{!! $show->detail !!}</div>
                            </div>
                            <div class="form-group">
                                <label>Foto Utama</label><br>
                                <img src="{{ asset('storage/img-produk/' . $show->foto) }}" class="foto-preview" width="100%" loading="lazy" decoding="async">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Foto Tambahan</label>
                            <div id="foto-container" data-gallery-url="{{ $galleryUrl }}">
                                <div class="alert alert-light border mb-0">Galeri foto akan dimuat setelah halaman tampil.</div>
                            </div>
                            <button type="button" class="btn btn-primary mt-3" id="toggle-upload-foto">Tambah Foto</button>
                            <div class="card border mt-3 d-none" id="upload-foto-card">
                                <div class="card-body">
                                    <form action="{{ route('backend.foto_produk.store') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="produk_id" value="{{ $show->id }}">
                                        <div class="form-group mb-2">
                                            <input type="file" name="foto_produk[]" accept=".jpg,.jpeg,.png,.gif" class="form-control" multiple>
                                            <small class="form-text text-muted">Anda bisa memilih lebih dari satu foto sekaligus.</small>
                                        </div>
                                        <button type="submit" class="btn btn-success">Simpan Foto</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-top">
                    <div class="card-body">
                        <a href="{{ route('backend.produk.index') }}">
                            <button type="button" class="btn btn-secondary">Kembali</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fotoContainer = document.getElementById('foto-container');
        const uploadButton = document.getElementById('toggle-upload-foto');
        const uploadCard = document.getElementById('upload-foto-card');

        if (uploadButton && uploadCard) {
            uploadButton.addEventListener('click', function() {
                uploadCard.classList.toggle('d-none');
            });
        }

        if (!fotoContainer) {
            return;
        }

        let galleryLoaded = false;

        async function loadGallery() {
            if (galleryLoaded) {
                return;
            }

            galleryLoaded = true;
            fotoContainer.innerHTML = '<div class="alert alert-light border mb-0">Memuat galeri foto...</div>';

            try {
                const response = await fetch(fotoContainer.dataset.galleryUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) {
                    throw new Error('Gagal memuat galeri.');
                }

                fotoContainer.innerHTML = await response.text();
            } catch (error) {
                fotoContainer.innerHTML = '<div class="alert alert-warning mb-0">Galeri foto belum dapat dimuat. Silakan muat ulang halaman.</div>';
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

            observer.observe(fotoContainer);
        } else {
            loadGallery();
        }
    });
</script>
@endsection
