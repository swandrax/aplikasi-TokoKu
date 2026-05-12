<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h1 class="display-4 text-danger">403</h1>
                        <h5 class="mb-3">Akses Ditolak</h5>
                        <p class="text-muted mb-4">Maaf, Anda tidak memiliki izin untuk membuka halaman ini.</p>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary mr-2">Kembali</a>
                        <a href="/" class="btn btn-primary">Beranda</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
