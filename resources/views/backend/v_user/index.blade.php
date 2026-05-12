@extends('backend.v_layouts.app')
@section('content')
<!-- contentAwal -->
<div class="row">
    <div class="col-12">
        <a href="{{ route('backend.user.create') }}">
            <button type="button" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</button>
        </a>
        <form action="{{ route('backend.user.index') }}" method="GET" class="form-inline mt-2 mb-2">
            <input type="text" name="q" value="{{ $keyword ?? '' }}" class="form-control mr-2" placeholder="Cari user...">
            <button type="submit" class="btn btn-info">Cari</button>
            @if (!empty($keyword))
            <a href="{{ route('backend.user.index') }}" class="btn btn-secondary ml-2">Reset</a>
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
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($index as $row)
                            <tr>
                                <td>{{ ($index->firstItem() ?? 1) + $loop->index }}</td>
                                <td>
                                    @if ($row->foto)
                                    <img src="{{ asset('storage/img-user/' . $row->foto) }}" width="40" class="rounded-circle" loading="lazy" decoding="async">
                                    @else
                                    <img src="{{ asset('storage/img-user/img-default.jpg') }}" width="40" class="rounded-circle" loading="lazy" decoding="async">
                                    @endif
                                </td>
                                <td>{{$row->nama}}</td>
                                <td>{{$row->email}}</td>
                                <td>
                                    @if ($row->role == 1)
                                    <span class="badge badge-success">Admin</span>
                                    @elseif($row->role == 0)
                                    <span class="badge badge-primary">User Admin</span>
                                    @elseif($row->role == 2)
                                    <span class="badge badge-warning">Customer</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($row->status == 1)
                                    <span class="badge badge-success">Aktif</span>
                                    @elseif($row->status == 0)
                                    <span class="badge badge-secondary">NonAktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('backend.user.edit', $row->id) }}" title="Ubah Data">
                                        <button type="button" class="btn btn-cyan btn-sm"><i class="far fa-edit"></i> Ubah</button>
                                    </a>
                                    <form method="POST" action="{{ route('backend.user.destroy', $row->id) }}" style="display: inline-block;">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm show_confirm" data-konf-delete="{{ $row->nama }}" title="Hapus Data">
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
<!-- contentAkhir -->
@endsection
