<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('image/icon_univ_bsi.png') }}">
    <title>tokoonline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f5; }
        .topbar { background-color: #2c3e50; padding: 10px 20px; color: white; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .topbar a { color: white; margin-right: 20px; text-decoration: none; }
        .topbar a:hover { color: #3498db; text-decoration: none; }
        .main-container { display: flex; }
        .sidebar { width: 250px; background-color: #34495e; color: white; min-height: calc(100vh - 60px); padding: 20px 0; box-shadow: 2px 0 4px rgba(0,0,0,0.1); }
        .sidebar a { display: block; padding: 12px 20px; color: white; text-decoration: none; border-left: 4px solid transparent; transition: all 0.3s; }
        .sidebar a:hover { background-color: #2c3e50; border-left-color: #3498db; color: #3498db; }
        .content { flex: 1; padding: 20px; }
        .card { box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .btn-group-vertical a { margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="topbar">
        <div>
            <a href="{{ route('backend.beranda') }}"><i class="fas fa-home"></i> Beranda</a>
            <a href="{{ route('backend.user.index') }}"><i class="fas fa-users"></i> User</a>
        </div>
        <div>
            <span>{{ Auth::user()->nama }}</span>
            <a href="" onclick="event.preventDefault(); document.getElementById('keluar-app').submit();" title="Keluar">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </a>
        </div>
    </div>

    <div class="main-container">
        <div class="sidebar">
            <h5 style="padding: 0 20px; margin-bottom: 20px;">Menu Utama</h5>
            <a href="{{ route('backend.beranda') }}"><i class="fas fa-home"></i> Beranda</a>
            <a href="{{ route('backend.user.index') }}"><i class="fas fa-users"></i> Data User</a>
        </div>

        <div class="content">
            <!-- @yieldAwal -->
            @yield('content')
            <!-- @yieldAkhir-->
        </div>
    </div>

    <!-- keluarApp -->
    <form id="keluar-app" action="{{ route('backend.logout') }}" method="POST" class="d-none">
        @csrf
    </form>
    <!-- keluarAppEnd -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <!-- sweetalert success-->
    @if (session('success'))
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        timer: 3000
    });
    </script>
    @endif
    <!-- sweetalert success End-->

    <script type="text/javascript">
    function hanyaAngka(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    // Konfirmasi delete
    $(document).on('click', '.show-confirm', function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var nama = $(this).data("nama");
        
        Swal.fire({
            title: 'Konfirmasi Hapus Data?',
            html: "Data yang dihapus <strong>" + nama + "</strong> tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, dihapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
    </script>
</body>
</html>

