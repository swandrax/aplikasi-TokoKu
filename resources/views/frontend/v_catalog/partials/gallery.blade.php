@if ($fotoProduk->isEmpty())
<div class="alert alert-secondary mt-3 mb-0">Belum ada foto tambahan untuk produk ini.</div>
@else
<div class="row">
    @foreach ($fotoProduk as $foto)
    <div class="col-4 mb-2">
        <img
            src="{{ asset('storage/img-produk/' . $foto->foto) }}"
            class="img-fluid rounded"
            alt="{{ $produk->nama_produk }}"
            loading="lazy"
            decoding="async">
    </div>
    @endforeach
</div>
@endif
