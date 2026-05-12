<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('image/icon_univ_bsi.png') }}">
    <title>tokoonline</title>
    <link href="{{ asset('backend/dist/css/style.min.css') }}" rel="stylesheet">
</head>
<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <div id="main-wrapper">
        <!-- Topbar header -->
        <header class="topbar" data-navbarbg="skin5">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header" data-logobg="skin5">
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                    <a class="navbar-brand" href="{{ route('backend.beranda') }}">
                        <b class="logo-icon p-l-10">
                            <img src="{{ asset('image/icon_univ_bsi.png') }}" alt="homepage" class="light-logo" />
                        </b>
                        <span class="logo-text">
                            <img src="{{ asset('image/logo_text.png') }}" alt="homepage" class="light-logo" />
                        </span>
                    </a>
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
                </div>
                <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
                    <ul class="navbar-nav float-left mr-auto">
                        <li class="nav-item d-none d-md-block">
                            <a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a>
                        </li>
                    </ul>
                    <ul class="navbar-nav float-right">
                        <!-- User profile and search -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                @if (Auth::user()->foto)
                                <img src="{{ asset('storage/img-user/' . Auth::user()->foto) }}" alt="user" class="rounded-circle" width="31" loading="lazy" decoding="async">
                                @else
                                <img src="{{ asset('storage/img-user/img-default.jpg') }}" alt="user" class="rounded-circle" width="31" loading="lazy" decoding="async">
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated">
                                <a class="dropdown-item" href="{{ route('backend.user.edit', Auth::user()->id) }}"><i class="ti-user m-r-5 m-l-5"></i> Profil Saya</a>
                                <a class="dropdown-item btn-logout" href="#" data-form-id="keluar-app"><i class="fa fa-power-off m-r-5 m-l-5"></i> Keluar</a>
                                <div class="dropdown-divider"></div>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- End Topbar -->

        <!-- Left Sidebar -->
        <aside class="left-sidebar" data-sidebarbg="skin5">
            <div class="scroll-sidebar">
                <!-- Sidebar navigation -->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav" class="p-t-30">
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('backend.beranda') }}" aria-expanded="false">
                                <i class="mdi mdi-view-dashboard"></i><span class="hide-menu">Beranda</span>
                            </a>
                        </li>
                        @if (Auth::user()->isAdmin())
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('backend.user.index') }}" aria-expanded="false">
                                <i class="mdi mdi-account"></i><span class="hide-menu">User</span>
                            </a>
                        </li>
                        @endif
                        @if (Auth::user()->isAdmin() || Auth::user()->isUserAdmin())
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-shopping"></i><span class="hide-menu">Data Produk</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="{{ route('backend.kategori.index') }}" class="sidebar-link">
                                        <i class="mdi mdi-chevron-right"></i><span class="hide-menu">Kategori</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="{{ route('backend.produk.index') }}" class="sidebar-link">
                                        <i class="mdi mdi-chevron-right"></i><span class="hide-menu">Produk</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark btn-logout" href="#" data-form-id="keluar-app" aria-expanded="false">
                                <i class="mdi mdi-logout"></i><span class="hide-menu">Logout</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
        </aside>
        <!-- End Left Sidebar -->

        <!-- Page wrapper -->
        <div class="page-wrapper">
            <div class="container-fluid">
                <!-- Start Page Content -->
                <!-- @yieldAwal -->
                @yield('content')
                <!-- @yieldAkhir -->
                <!-- End Page Content -->
            </div>
            <footer class="footer text-center">
                Web Programming. Studi Kasus Toko Online <a href="https://bsi.ac.id/">Kuliah..? BSI Aja !!!</a>
            </footer>
        </div>
        <!-- End Page wrapper -->
    </div>

    <!-- All Jquery -->
    <script src="{{ asset('backend/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/libs/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('backend/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('backend/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js') }}"></script>
    <script src="{{ asset('backend/extra-libs/sparkline/sparkline.js') }}"></script>
    <script src="{{ asset('backend/dist/js/waves.js') }}"></script>
    <script src="{{ asset('backend/dist/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('backend/dist/js/custom.min.js') }}"></script>
    <!-- form keluar app -->
    <form id="keluar-app" action="{{ route('backend.logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- SweetAlert -->
    {{-- TODO: letakkan file aset di public/sweetalert/sweetalert2.all.min.js jika belum tersedia --}}
    <script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>
    <script>
        function showModalAlert(icon, title, text, htmlContent = null) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: icon || 'info',
                    title: title || 'Informasi',
                    text: htmlContent ? undefined : (text || ''),
                    html: htmlContent || undefined,
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

    @if ($errors->any())
    <script>
        const formErrorList = @json($errors->all());
        const listHtml = '<ul class="text-left mb-0">' + formErrorList.map(function(item) {
            return '<li>' + item + '</li>';
        }).join('') + '</ul>';
        showModalAlert('warning', 'Validasi Gagal', '', listHtml);
    </script>
    @endif

    @if (!empty($usesCkeditor))
    <!-- CKEditor -->
    {{-- TODO: letakkan file aset di public/ckeditor/ckeditor.js jika belum tersedia --}}
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        if (typeof ClassicEditor !== 'undefined' && document.querySelector('#ckeditor')) {
            ClassicEditor.create(document.querySelector('#ckeditor')).catch(error => { console.error(error); });
        }
    </script>
    @endif

    <!-- Preview Foto -->
    <script>
        function previewFoto() {
            const fotoInput = document.querySelector('input[name="foto"]');
            const fotoPreview = document.querySelector('.foto-preview');
            if (fotoInput && fotoPreview) {
                const file = fotoInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) { fotoPreview.src = e.target.result; };
                    reader.readAsDataURL(file);
                }
            }
        }

        function hanyaAngka(event) {
            return (event.charCode >= 48 && event.charCode <= 57);
        }
    </script>

    <!-- Konfirmasi delete -->
    <script type="text/javascript">
        $('.btn-logout').click(function(event) {
            event.preventDefault();
            var formId = $(this).data('form-id') || 'keluar-app';
            confirmLogout(formId);
        });

        $('.show_confirm').click(function(event) {
            var form = $(this).closest("form");
            var konfdelete = $(this).data("konf-delete");
            event.preventDefault();

            if (typeof Swal === 'undefined') {
                form.submit();
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Hapus Data?',
                html: "Data yang dihapus <strong>" + konfdelete + "</strong> tidak dapat dikembalikan!",
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
