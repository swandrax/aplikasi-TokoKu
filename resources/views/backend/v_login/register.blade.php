<!DOCTYPE html>
<html dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('image/icon_univ_bsi.png') }}">
    <title>tokoonline - Register</title>
    <link href="{{ asset('backend/dist/css/style.min.css') }}" rel="stylesheet">
</head>
<body>
    <div class="main-wrapper">
        <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div>
        <div class="auth-wrapper d-flex no-block justify-content-center align-items-center bg-dark">
            <div class="auth-box bg-dark border-top border-secondary">
                <div class="text-center p-t-20 p-b-20">
                    <span class="db"><img src="{{ asset('image/logo.png') }}" alt="logo" /></span>
                </div>

                <form class="form-horizontal m-t-20" action="{{ route('backend.register.store') }}" method="post">
                    @csrf
                    <div class="row p-b-30">
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-white"><i class="ti-user"></i></span>
                                </div>
                                <input type="text" name="nama" value="{{ old('nama') }}" class="form-control form-control-lg @error('nama') is-invalid @enderror" placeholder="Masukkan Nama">
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-success text-white"><i class="ti-email"></i></span>
                                </div>
                                <input type="text" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="Masukkan Email">
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-info text-white"><i class="ti-id-badge"></i></span>
                                </div>
                                <select name="role" class="form-control form-control-lg @error('role') is-invalid @enderror">
                                    <option value="">- Pilih Role -</option>
                                    <option value="1" {{ old('role') == '1' ? 'selected' : '' }}>Admin</option>
                                    <option value="2" {{ old('role') == '2' ? 'selected' : '' }}>Customer</option>
                                </select>
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-warning text-white"><i class="ti-mobile"></i></span>
                                </div>
                                <input type="text" name="hp" value="{{ old('hp') }}" class="form-control form-control-lg @error('hp') is-invalid @enderror" placeholder="Masukkan Nomor HP">
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-danger text-white"><i class="ti-lock"></i></span>
                                </div>
                                <input type="password" id="register-password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Masukkan Password">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-secondary toggle-password" data-target="register-password" aria-label="Tampilkan password">Lihat</button>
                                </div>
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-secondary text-white"><i class="ti-check-box"></i></span>
                                </div>
                                <input type="password" id="register-password-confirmation" name="password_confirmation" class="form-control form-control-lg" placeholder="Konfirmasi Password">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-secondary toggle-password" data-target="register-password-confirmation" aria-label="Tampilkan konfirmasi password">Lihat</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row border-top border-secondary">
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <div class="p-t-20">
                                    <a href="{{ route('backend.login') }}" class="btn btn-info">Kembali Login</a>
                                    <button class="btn btn-success float-right" type="submit">Daftar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('backend/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/libs/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('backend/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
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

        $('.preloader').fadeOut();

        document.querySelectorAll('.toggle-password').forEach(function(button) {
            button.addEventListener('click', function() {
                var target = document.getElementById(button.getAttribute('data-target'));
                if (!target) {
                    return;
                }

                var isHidden = target.type === 'password';
                target.type = isHidden ? 'text' : 'password';
                button.textContent = isHidden ? 'Sembunyikan' : 'Lihat';
                button.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
            });
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
    @endif

    @if ($errors->any())
    <script>
        const formErrorList = @json($errors->all());
        const listHtml = '<ul class="text-left mb-0">' + formErrorList.map(function(item) {
            return '<li>' + item + '</li>';
        }).join('') + '</ul>';
        showModalAlert('warning', 'Validasi Register Gagal', '', listHtml);
    </script>
    @endif
</body>
</html>
