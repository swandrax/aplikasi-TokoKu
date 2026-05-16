<!DOCTYPE html>
<html dir="ltr">
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
    <div class="main-wrapper">
        <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div>
        <div class="auth-wrapper d-flex no-block justify-content-center align-items-center bg-dark">
            <div class="auth-box bg-dark border-top border-secondary">
                <div id="loginform">
                    <div class="text-center p-t-20 p-b-20">
                        <span class="db"><img src="{{ asset('image/logo.png') }}" alt="logo" /></span>
                    </div>
                    <form class="form-horizontal m-t-20" id="loginform" action="{{ route('backend.login.authenticate') }}" method="post">
                        @csrf
                        <div class="row p-b-30">
                            <div class="col-12">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-success text-white" id="basic-addon1"><i class="ti-user"></i></span>
                                    </div>
                                    <input type="text" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="Masukkan Email" aria-label="Username" aria-describedby="basic-addon1">
                                    @error('email')
                                    <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-warning text-white" id="basic-addon2"><i class="ti-pencil"></i></span>
                                    </div>
                                    <input type="password" id="login-password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Masukkan Password" aria-label="Password" aria-describedby="basic-addon1">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-secondary toggle-password" data-target="login-password" aria-label="Tampilkan password">Lihat</button>
                                    </div>
                                    @error('password')
                                    <span class="invalid-feedback alert-danger d-block" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row border-top border-secondary">
                            <div class="col-12">
                                <div class="form-group mb-0">
                                    <div class="p-t-20 d-flex justify-content-between align-items-center">
                                        <div>
                                            <a class="btn btn-primary mr-2" href="{{ route('backend.register') }}"><i class="fa fa-user-plus m-r-5"></i> Register</a>
                                            <button class="btn btn-info" id="to-recover" type="button"><i class="fa fa-lock m-r-5"></i> Lost password?</button>
                                        </div>
                                        <button class="btn btn-success" type="submit">Login</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="recoverform">
                    <div class="text-center">
                        <span class="text-white">Enter your e-mail address below and we will send you instructions how to recover a password.</span>
                    </div>
                    <div class="row m-t-20">
                        <form class="col-12" action="{{ route('backend.password.email') }}" method="POST">
                            @csrf
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-danger text-white" id="basic-addon1"><i class="ti-email"></i></span>
                                </div>
                                <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="Email Address" aria-label="Username" aria-describedby="basic-addon1" required>
                            </div>
                            <div class="row m-t-20 p-t-20 border-top border-secondary">
                                <div class="col-12 d-flex justify-content-between">
                                    <a class="btn btn-success" href="#" id="to-login" name="action">Back To Login</a>
                                    <button class="btn btn-info" type="submit" name="action">Recover</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.style.display = 'none';
            }
        });
    </script>
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

        $('[data-toggle="tooltip"]').tooltip();
        $('.preloader').fadeOut();
        $('#to-recover').on('click', function() {
            $('#loginform').slideUp();
            $('#recoverform').fadeIn();
        });
        $('#to-login').click(function() {
            $('#recoverform').hide();
            $('#loginform').fadeIn();
        });

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
</body>
</html>
