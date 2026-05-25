<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringatan Stok Kritis</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #ffffff;
            text-align: center;
            padding: 30px 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 20px 0;
        }
        .alert-box {
            background-color: #fff5f5;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .alert-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }
        .alert-row:last-child {
            margin-bottom: 0;
            border-top: 1px solid #fee2e2;
            padding-top: 12px;
            font-weight: 700;
            font-size: 16px;
        }
        .footer {
            background-color: #f8fafc;
            text-align: center;
            padding: 20px 30px;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Peringatan Stok Kritis (Low Stock Alert)</h1>
        </div>
        <div class="content">
            <p>Halo, <strong>Administrator TokoKu</strong>!</p>
            <p>Sistem mendeteksi bahwa stok untuk produk berikut telah berada di bawah batas minimum (5 unit):</p>
            
            <div class="alert-box">
                <div class="alert-row">
                    <span>Nama Produk</span>
                    <strong>{{ $productName }}</strong>
                </div>
                <div class="alert-row">
                    <span>Harga Jual</span>
                    <strong>Rp {{ $price }}</strong>
                </div>
                <div class="alert-row">
                    <span>Sisa Stok Tersedia</span>
                    <strong style="color: #ef4444;">{{ $currentStock }} Unit</strong>
                </div>
            </div>
            
            <p>Silakan segera lakukan pengadaan stok (restock) baru untuk produk ini guna menghindari kehabisan stok bagi pembeli.</p>
            <p style="text-align: center; margin-top: 30px;">
                <a href="{{ route('login') }}" style="background-color: #ef4444; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: 700; display: inline-block;">Masuk ke Panel Admin</a>
            </p>
        </div>
        <div class="footer">
            <p>Sistem Notifikasi Otomatis TokoKu &copy; 2026.</p>
        </div>
    </div>
</body>
</html>
