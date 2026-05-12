@extends('backend.v_layouts.app')
@section('content')
<div class="row">
    <div class="col-12">
        <a href="{{ route('backend.produk.create') }}">
            <button type="button" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</button>
        </a>
        <form action="{{ route('backend.produk.index') }}" method="GET" class="form-inline mt-2 mb-2">
            <input type="text" name="q" value="{{ $keyword ?? '' }}" class="form-control mr-2" placeholder="Cari produk...">
            <button type="submit" class="btn btn-info">Cari</button>
            @if (!empty($keyword))
            <a href="{{ route('backend.produk.index') }}" class="btn btn-secondary ml-2">Reset</a>
            @endif
        </form>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"> {{$judul}} </h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($index as $row)
                            <tr>
                                <td>{{ ($index->firstItem() ?? 1) + $loop->index }}</td>
                                <td>{{ $row->kategori->nama_kategori }}</td>
                                <td>
                                    @if ($row->status == 1)
                                    <span class="badge badge-success">Publis</span>
                                    @else
                                    <span class="badge badge-secondary">Blok</span>
                                    @endif
                                </td>
                                <td>{{ $row->nama_produk }}</td>
                                <td>Rp. {{ number_format($row->harga, 0, ',', '.') }}</td>
                                <td>{{ $row->stok }}</td>
                                <td>
                                    <a href="{{ route('backend.produk.edit', $row->id) }}" title="Ubah Data">
                                        <button type="button" class="btn btn-cyan btn-sm"><i class="far fa-edit"></i> Ubah</button>
                                    </a>
                                    <a href="{{ route('backend.produk.show', $row->id) }}" title="Tambah Gambar">
                                        <button type="button" class="btn btn-warning btn-sm"><i class="fas fa-plus"></i> Gambar</button>
                                    </a>
                                    <form method="POST" action="{{ route('backend.produk.destroy', $row->id) }}" style="display: inline-block;">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm show_confirm" data-konf-delete="{{ $row->nama_produk }}" title="Hapus Data">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-3">
            {{ $index->links() }}
        </div>
    </div>
</div>
@endsection
