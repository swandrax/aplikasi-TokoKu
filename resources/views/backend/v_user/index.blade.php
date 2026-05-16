@extends('backend.v_layouts.app')
@section('content')
<div class="row">
    <div class="col-12">
        <button type="button" class="btn btn-primary" onclick="addForm()"><i class="fas fa-plus"></i> Tambah (Realtime)</button>

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
                    <table class="table table-striped table-bordered" id="user-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>HP</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($index as $row)
                            <tr id="row-{{ $row->id }}">
                                <td>{{ ($index->firstItem() ?? 1) + $loop->index }}</td>
                                <td>
                                    @if ($row->foto)
                                    <img src="{{ asset('storage/img-user/' . $row->foto) }}" width="50" class="rounded-circle">
                                    @else
                                    <img src="{{ asset('storage/img-user/img-default.jpg') }}" width="50" class="rounded-circle">
                                    @endif
                                </td>
                                <td>{{ $row->nama }}</td>
                                <td>{{ $row->email }}</td>
                                <td>{{ $row->hp }}</td>
                                <td>
                                    @if ($row->role == 1)
                                    <span class="badge badge-success">Admin</span>
                                    @elseif($row->role == 2)
                                    <span class="badge badge-info">User Admin</span>
                                    @else
                                    <span class="badge badge-warning">Customer</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($row->status == 1)
                                    <span class="badge badge-primary">Aktif</span>
                                    @else
                                    <span class="badge badge-danger">Non-Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-cyan btn-sm" onclick="editForm({{ $row->id }})"><i class="far fa-edit"></i> Ubah</button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteData({{ $row->id }}, '{{ $row->nama }}')"><i class="fas fa-trash"></i> Hapus</button>
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

<!-- Modal CRUD User -->
<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="form-user" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="method">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Form User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama" id="nama" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>No HP</label>
                                <input type="text" name="hp" id="hp" class="form-control" required maxlength="13" onkeypress="return hanyaAngka(event)">
                            </div>
                            <div id="password-section">
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="password" id="password" class="form-control">
                                    <small class="text-muted">Min 4 karakter, kombinasi A-z, 0-9, Simbol</small>
                                </div>
                                <div class="form-group">
                                    <label>Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" id="role" class="form-control" required>
                                    <option value="1">Admin</option>
                                    <option value="2">User Admin</option>
                                    <option value="0">Customer</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1">Aktif</option>
                                    <option value="0">Non-Aktif</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Foto Profil</label>
                                <input type="file" name="foto" id="foto" class="form-control" onchange="previewFotoUser()">
                                <div class="mt-2 text-center">
                                    <img src="{{ asset('storage/img-user/img-default.jpg') }}" id="preview-user" class="img-thumbnail rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                                </div>
                            </div>
                        </div>
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
    function previewFotoUser() {
        const input = document.getElementById('foto');
        const preview = document.getElementById('preview-user');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function addForm() {
        $('#modal-form').modal('show');
        $('#modal-title').text('Tambah User Baru');
        $('#form-user')[0].reset();
        $('#method').val('POST');
        $('#id').val('');
        $('#password-section').show();
        $('#password').attr('required', true);
        $('#password_confirmation').attr('required', true);
        $('#preview-user').attr('src', "{{ asset('storage/img-user/img-default.jpg') }}");
    }

    function editForm(id) {
        $('#modal-form').modal('show');
        $('#modal-title').text('Ubah Data User');
        $('#method').val('PUT');
        $('#id').val(id);
        $('#password-section').hide(); // Sembunyikan ganti password di modal ini untuk keamanan sederhana
        $('#password').attr('required', false);
        $('#password_confirmation').attr('required', false);

        $.ajax({
            url: "{{ url('backend/user') }}/" + id + "/edit",
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#nama').val(data.nama);
                $('#email').val(data.email);
                $('#hp').val(data.hp);
                $('#role').val(data.role);
                $('#status').val(data.status);
                if (data.foto) {
                    $('#preview-user').attr('src', "{{ asset('storage/img-user') }}/" + data.foto);
                } else {
                    $('#preview-user').attr('src', "{{ asset('storage/img-user/img-default.jpg') }}");
                }
            },
            error: function() {
                Swal.fire('Error', 'Tidak dapat mengambil data', 'error');
            }
        });
    }

    $('#form-user').on('submit', function(e) {
        e.preventDefault();
        const id = $('#id').val();
        const method = $('#method').val();
        const url = method === 'POST' ? "{{ route('backend.user.store') }}" : "{{ url('backend/user') }}/" + id;

        let formData = new FormData(this);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                $('#modal-form').modal('hide');
                Swal.fire('Berhasil', data.message, 'success').then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMsg = '';
                if (errors) {
                    for (let key in errors) {
                        errorMsg += errors[key][0] + '<br>';
                    }
                } else {
                    errorMsg = xhr.responseJSON.message || 'Terjadi kesalahan sistem';
                }
                Swal.fire({
                    title: 'Gagal',
                    html: errorMsg,
                    icon: 'error'
                });
            }
        });
    });

    function deleteData(id, nama) {
        Swal.fire({
            title: 'Hapus User?',
            text: "Hapus akun " + nama + "? Tindakan ini tidak bisa dibatalkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('backend/user') }}/" + id,
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
                    error: function(xhr) {
                        Swal.fire('Gagal', xhr.responseJSON.message || 'Error saat menghapus', 'error');
                    }
                });
            }
        });
    }
</script>
@endsection
