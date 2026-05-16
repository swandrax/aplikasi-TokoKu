@extends('backend.v_layouts.app')
@section('content')
<div class="row">
    <div class="col-12">
        <button type="button" class="btn btn-primary" onclick="addForm()"><i class="fas fa-plus"></i> Tambah (Realtime)</button>

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
                    <table class="table table-striped table-bordered" id="kategori-table">
                        <thead>
                            <tr><th>No</th><th>Nama Kategori</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($index as $row)
                            <tr id="row-{{ $row->id }}">
                                <td>{{ ($index->firstItem() ?? 1) + $loop->index }}</td>
                                <td class="nama-kategori">{{$row->nama_kategori}}</td>
                                <td>
                                    <button type="button" class="btn btn-cyan btn-sm" onclick="editForm({{ $row->id }})"><i class="far fa-edit"></i> Ubah</button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteData({{ $row->id }}, '{{ $row->nama_kategori }}')"><i class="fas fa-trash"></i> Hapus</button>
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

<!-- Modal CRUD -->
<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="form-kategori">
                @csrf
                <input type="hidden" name="_method" id="method">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Form Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function addForm() {
        $('#modal-form').modal('show');
        $('#modal-title').text('Tambah Kategori');
        $('#form-kategori')[0].reset();
        $('#method').val('POST');
        $('#id').val('');
    }

    function editForm(id) {
        $('#modal-form').modal('show');
        $('#modal-title').text('Ubah Kategori');
        $('#method').val('PUT');
        $('#id').val(id);

        $.ajax({
            url: "{{ url('backend/kategori') }}/" + id + "/edit",
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#nama_kategori').val(data.nama_kategori);
            },
            error: function() {
                Swal.fire('Error', 'Tidak dapat mengambil data', 'error');
            }
        });
    }

    $('#form-kategori').on('submit', function(e) {
        e.preventDefault();
        const id = $('#id').val();
        const method = $('#method').val();
        const url = method === 'POST' ? "{{ route('backend.kategori.store') }}" : "{{ url('backend/kategori') }}/" + id;

        $.ajax({
            url: url,
            type: "POST",
            data: $('#form-kategori').serialize(),
            success: function(data) {
                $('#modal-form').modal('hide');
                Swal.fire('Berhasil', data.message, 'success').then(() => {
                    location.reload(); // Untuk update nomor urut & pagination tetap akurat
                });
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMsg = '';
                for (let key in errors) {
                    errorMsg += errors[key][0] + '\n';
                }
                Swal.fire('Gagal', errorMsg, 'error');
            }
        });
    });

    function deleteData(id, nama) {
        Swal.fire({
            title: 'Hapus Kategori?',
            text: "Anda akan menghapus kategori: " + nama,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('backend/kategori') }}/" + id,
                    type: "POST",
                    data: {
                        '_method': 'DELETE',
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        Swal.fire('Dihapus!', data.message, 'success').then(() => {
                            location.reload();
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghapus data', 'error');
                    }
                });
            }
        });
    }
</script>
@endsection
