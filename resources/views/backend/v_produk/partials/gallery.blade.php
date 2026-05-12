@if ($fotoProduk->isEmpty())
<div class="alert alert-secondary mb-0">Belum ada foto tambahan untuk produk ini.</div>
@else
<div class="row">
    @foreach ($fotoProduk as $gambar)
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <img
                src="{{ asset('storage/img-produk/' . $gambar->foto) }}"
                width="100%"
                alt="Foto produk"
                loading="lazy"
                decoding="async">
            <div class="card-body p-2">
                <form action="{{ route('backend.foto_produk.destroy', $gambar->id) }}" method="post">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn btn-danger btn-sm btn-block">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
