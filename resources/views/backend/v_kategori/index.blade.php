@extends('backend.v_layouts.app')
@section('content')
<div class="row">
    <div class="col-12">
        <a href="{{ route('backend.kategori.create') }}">
            <button type="button" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</button>
        </a>
        <form action="{{ route('backend.kategori.index') }}" method="GET" class="form-inline mt-2 mb-2">
            <input type="text" name="q" value="{{ $keyword ?? '' }}" class="form-control mr-2" placeholder="Cari kategori...">
            <button type="submit" class="btn btn-info">Cari</button>
            @if (!empty($keyword))
            <a href="{{ route('backend.kategori.index') }}" class="btn btn-secondary ml-2">Reset</a>
            @endif
        </form>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"> {{$judul}} </h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr><th>No</th><th>Nama Kategori</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($index as $row)
                            <tr>
                                <td>{{ ($index->firstItem() ?? 1) + $loop->index }}</td>
                                <td>{{$row->nama_kategori}}</td>
                                <td>
                                    <a href="{{ route('backend.kategori.edit', $row->id) }}" title="Ubah Data">
                                        <button type="button" class="btn btn-cyan btn-sm"><i class="far fa-edit"></i> Ubah</button>
                                    </a>
                                    <form method="POST" action="{{ route('backend.kategori.destroy', $row->id) }}" style="display: inline-block;">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm show_confirm" data-konf-delete="{{ $row->nama_kategori }}" title="Hapus Data">
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
