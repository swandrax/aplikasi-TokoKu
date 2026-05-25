<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan TokoKu</title>
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
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            text-align: center;
            padding: 30px 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
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
        .order-summary {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .order-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }
        .order-row:last-child {
            margin-bottom: 0;
            border-top: 1px solid #e2e8f0;
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
            <h1>Pembayaran Berhasil</h1>
        </div>
        <div class="content">
            <p>Halo, <strong>{{ $name }}</strong>!</p>
            <p>Terima kasih telah berbelanja di TokoKu Store. Pembayaran Anda untuk pesanan <strong>{{ $orderNumber }}</strong> telah kami terima dan sukses diproses.</p>
            
            <div class="order-summary">
                <div class="order-row">
                    <span>No. Transaksi</span>
                    <strong>{{ $orderNumber }}</strong>
                </div>
                <div class="order-row">
                    <span>Metode Pembayaran</span>
                    <strong>{{ $paymentMethod }}</strong>
                </div>
                <div class="order-row">
                    <span>Status</span>
                    <strong style="color: #10b981;">LUNAS / PAID</strong>
                </div>
                <div class="order-row">
                    <span>Total Pembayaran</span>
                    <strong>Rp {{ $total }}</strong>
                </div>
            </div>
            
            <p>Detail struk belanja resmi telah kami lampirkan bersama email ini dalam bentuk PDF. Anda dapat mengunduh atau mencetaknya untuk bukti transaksi yang sah.</p>
            <p>Jika ada pertanyaan atau kendala, silakan hubungi tim customer service kami melalui menu Bantuan di aplikasi.</p>
        </div>
        <div class="footer">
            <p>Tim TokoKu Store &copy; 2026. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
