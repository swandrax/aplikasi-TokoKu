<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Toko Online - Customer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #f7f8fb; }
        .card-product { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-product:hover { transform: translateY(-3px); box-shadow: 0 0.6rem 1rem rgba(0,0,0,0.1); }
        .product-image { object-fit: cover; height: 220px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('frontend.catalog.index') }}">Toko Online</a>
            <div class="ml-auto d-flex align-items-center">
                <span class="text-white mr-3">{{ Auth::user()->nama }} ({{ Auth::user()->roleLabel() }})</span>
                <button type="button" class="btn btn-outline-light btn-sm btn-logout" data-form-id="logout-form">
                    Keluar
                </button>
            </div>
        </div>
    </nav>

    <main class="container pb-5">
        @yield('content')
    </main>

    <form id="logout-form" action="{{ route('backend.logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>
    <script>
        function showModalAlert(icon, title, text) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: icon || 'info',
                    title: title || 'Informasi',
                    text: text || '',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const message = [title, text].filter(Boolean).join("\n");
            if (message) {
                alert(message);
            }
        }

        function confirmLogout(formId) {
            const form = document.getElementById(formId);
            if (!form) {
                return;
            }

            if (typeof Swal === 'undefined') {
                form.submit();
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Logout',
                text: 'Apakah Anda yakin ingin keluar dari aplikasi?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        $('.btn-logout').on('click', function(event) {
            event.preventDefault();
            confirmLogout($(this).data('form-id') || 'logout-form');
        });
    </script>

    @if (session('alert'))
    <script>
        showModalAlert(
            @json(session('alert.type')),
            @json(session('alert.title')),
            @json(session('alert.text'))
        );
    </script>
    @elseif (session('success'))
    <script>
        showModalAlert('success', 'Berhasil', @json(session('success')));
    </script>
    @elseif (session('error'))
    <script>
        showModalAlert('error', 'Terjadi Kesalahan', @json(session('error')));
    </script>
    @endif
</body>
</html>
