<!DOCTYPE html>
<html dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('image/icon_univ_bsi.png') }}">
    <title>tokoonline - Reset Password</title>
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

                <form class="form-horizontal m-t-20" action="{{ route('backend.password.update') }}" method="post">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="row p-b-30">
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-danger text-white"><i class="ti-email"></i></span>
                                </div>
                                <input type="email" name="email" value="{{ $email ?? old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="Email Address" required>
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-warning text-white"><i class="ti-pencil"></i></span>
                                </div>
                                <input type="password" id="reset-password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="New Password" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-secondary toggle-password" data-target="reset-password">Lihat</button>
                                </div>
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-info text-white"><i class="ti-check-box"></i></span>
                                </div>
                                <input type="password" id="reset-password-confirmation" name="password_confirmation" class="form-control form-control-lg" placeholder="Confirm New Password" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-secondary toggle-password" data-target="reset-password-confirmation">Lihat</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row border-top border-secondary">
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <div class="p-t-20">
                                    <button class="btn btn-success float-right" type="submit">Reset Password</button>
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
        showModalAlert('warning', 'Validasi Gagal', '', listHtml);
    </script>
    @endif
</body>
</html>
