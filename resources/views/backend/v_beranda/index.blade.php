@extends('backend.v_layouts.app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body border-top">
                <h5 class="card-title"> {{$judul}}</h5>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">Selamat Datang, {{ Auth::user()->nama }}</h4>
                    Aplikasi Toko Online dengan hak akses yang anda miliki sebagai
                    <b>{{ Auth::user()->roleLabel() }}</b>.
                    <hr>
                    <p class="mb-0">Data dashboard akan diperbarui otomatis setiap 10 detik.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 col-sm-6">
        <div class="card card-hover">
            <div class="box bg-cyan text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-account"></i></h1>
                <h6 class="text-white">Total User</h6>
                <h3 class="text-white" id="stat-user">{{ $summary['total_user'] ?? '...' }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-hover">
            <div class="box bg-success text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-view-list"></i></h1>
                <h6 class="text-white">Total Kategori</h6>
                <h3 class="text-white" id="stat-kategori">{{ $summary['total_kategori'] ?? '...' }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-hover">
            <div class="box bg-warning text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-shopping"></i></h1>
                <h6 class="text-white">Total Produk</h6>
                <h3 class="text-white" id="stat-produk">{{ $summary['total_produk'] ?? '...' }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-hover">
            <div class="box bg-danger text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-tag-heart"></i></h1>
                <h6 class="text-white">Produk Aktif</h6>
                <h3 class="text-white" id="stat-produk-aktif">{{ $summary['total_produk_aktif'] ?? '...' }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title mb-0">Update Produk Terbaru</h5>
                    <small class="text-muted">Sinkron terakhir: <span id="last-sync">{{ $summary['server_time'] ?? 'memuat...' }}</span></small>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <th>Update</th>
                            </tr>
                        </thead>
                        <tbody id="realtime-produk-body">
                            <tr>
                                <td colspan="5" class="text-center">Data dashboard akan dimuat setelah halaman tampil.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const realtimeUrl = "{{ route('backend.realtime.summary') }}";
        const userElement = document.getElementById('stat-user');
        const kategoriElement = document.getElementById('stat-kategori');
        const produkElement = document.getElementById('stat-produk');
        const produkAktifElement = document.getElementById('stat-produk-aktif');
        const syncElement = document.getElementById('last-sync');
        const produkBody = document.getElementById('realtime-produk-body');

        function toRupiah(angka) {
            return 'Rp. ' + Number(angka).toLocaleString('id-ID');
        }

        function renderProdukRows(rows) {
            if (!produkBody) {
                return;
            }

            if (!rows || rows.length === 0) {
                produkBody.innerHTML = '<tr><td colspan="5" class="text-center">Belum ada data produk.</td></tr>';
                return;
            }

            produkBody.innerHTML = rows.map(function(item) {
                return '<tr>'
                    + '<td>' + item.nama_produk + '</td>'
                    + '<td>' + item.kategori + '</td>'
                    + '<td>' + item.stok + '</td>'
                    + '<td>' + toRupiah(item.harga) + '</td>'
                    + '<td>' + (item.updated_at || '-') + '</td>'
                    + '</tr>';
            }).join('');
        }

        async function refreshSummary() {
            try {
                const response = await fetch(realtimeUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) {
                    return;
                }

                const data = await response.json();
                if (userElement) userElement.textContent = data.total_user ?? 0;
                if (kategoriElement) kategoriElement.textContent = data.total_kategori ?? 0;
                if (produkElement) produkElement.textContent = data.total_produk ?? 0;
                if (produkAktifElement) produkAktifElement.textContent = data.total_produk_aktif ?? 0;
                if (syncElement) syncElement.textContent = data.server_time ?? '-';
                renderProdukRows(data.produk_terbaru || []);
            } catch (error) {
                console.error('Gagal mengambil data realtime dashboard:', error);
            }
        }

        function scheduleInitialRefresh() {
            if ('requestIdleCallback' in window) {
                window.requestIdleCallback(refreshSummary);
                return;
            }

            setTimeout(refreshSummary, 0);
        }

        scheduleInitialRefresh();

        setInterval(function() {
            if (document.visibilityState === 'visible') {
                refreshSummary();
            }
        }, 10000);
    });
</script>
@endsection
