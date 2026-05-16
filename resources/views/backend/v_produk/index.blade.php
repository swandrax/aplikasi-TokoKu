@extends('backend.v_layouts.app')
@section('content')
<div class="row">
    <div class="col-12">
        <button type="button" class="btn btn-primary" onclick="addForm()"><i class="fas fa-plus"></i> Tambah (Realtime)</button>

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
                    <table class="table table-striped table-bordered" id="produk-table">
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
                            <tr id="row-{{ $row->id }}">
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
                                    <button type="button" class="btn btn-cyan btn-sm" onclick="editForm({{ $row->id }})"><i class="far fa-edit"></i> Ubah</button>
                                    <a href="{{ route('backend.produk.show', $row->id) }}" title="Tambah Gambar">
                                        <button type="button" class="btn btn-warning btn-sm"><i class="fas fa-plus"></i> Gambar</button>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteData({{ $row->id }}, '{{ $row->nama_produk }}')"><i class="fas fa-trash"></i> Hapus</button>
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

<!-- Modal CRUD Produk -->
<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="form-produk" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="method">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Form Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="kategori_id" id="kategori_id" class="form-control" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($kategori as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nama Produk</label>
                                <input type="text" name="nama_produk" id="nama_produk" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Harga</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">Rp.</span></div>
                                    <input type="number" name="harga" id="harga" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Stok</label>
                                <input type="number" name="stok" id="stok" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Foto Utama</label>
                                <input type="file" name="foto" id="foto" class="form-control" onchange="previewFotoRealtime()">
                                <div class="mt-2 text-center">
                                    <img src="{{ asset('storage/img-produk/img-default.jpg') }}" id="preview-img" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1">Publis</option>
                                    <option value="0">Blok</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Detail Produk</label>
                        <textarea name="detail" id="detail" class="form-control" rows="3"></textarea>
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
    function previewFotoRealtime() {
        const input = document.getElementById('foto');
        const preview = document.getElementById('preview-img');
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
        $('#modal-title').text('Tambah Produk');
        $('#form-produk')[0].reset();
        $('#method').val('POST');
        $('#id').val('');
        $('#preview-img').attr('src', "{{ asset('storage/img-produk/img-default.jpg') }}");
    }

    function editForm(id) {
        $('#modal-form').modal('show');
        $('#modal-title').text('Ubah Produk');
        $('#method').val('PUT');
        $('#id').val(id);

        $.ajax({
            url: "{{ url('backend/produk') }}/" + id + "/edit",
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#kategori_id').val(data.kategori_id);
                $('#nama_produk').val(data.nama_produk);
                $('#harga').val(data.harga);
                $('#stok').val(data.stok);
                $('#status').val(data.status);
                $('#detail').val(data.detail);
                if (data.foto) {
                    $('#preview-img').attr('src', "{{ asset('storage/img-produk') }}/" + data.foto);
                } else {
                    $('#preview-img').attr('src', "{{ asset('storage/img-produk/img-default.jpg') }}");
                }
            },
            error: function() {
                Swal.fire('Error', 'Tidak dapat mengambil data', 'error');
            }
        });
    }

    $('#form-produk').on('submit', function(e) {
        e.preventDefault();
        const id = $('#id').val();
        const method = $('#method').val();
        const url = method === 'POST' ? "{{ route('backend.produk.store') }}" : "{{ url('backend/produk') }}/" + id;

        // Gunakan FormData untuk pengiriman file
        let formData = new FormData(this);

        $.ajax({
            url: url,
            type: "POST", // Selalu POST, Laravel akan baca _method
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
            title: 'Hapus Produk?',
            text: "Anda akan menghapus produk: " + nama,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('backend/produk') }}/" + id,
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
