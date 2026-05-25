<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi TokoKu</title>
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
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #ffffff;
            text-align: center;
            padding: 30px 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 20px 0;
        }
        .otp-box {
            background-color: #f1f5f9;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            display: inline-block;
            margin: 20px 0;
            padding: 16px 40px;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 0.15em;
            color: #4f46e5;
            margin: 0;
        }
        .expiry-alert {
            font-size: 14px;
            color: #ef4444;
            background-color: #fef2f2;
            border-radius: 8px;
            padding: 10px 16px;
            display: inline-block;
            margin-top: 10px;
            font-weight: 500;
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
            margin: 0 0 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>TokoKu</h1>
        </div>
        <div class="content">
            <p>Halo, <strong>{{ $name }}</strong>!</p>
            <p>Terima kasih telah mendaftar di TokoKu Store. Silakan gunakan kode OTP di bawah ini untuk memverifikasi akun Anda:</p>
            
            <div class="otp-box">
                <h2 class="otp-code">{{ $otp }}</h2>
            </div>
            
            <br>
            <div class="expiry-alert">
                Kode berlaku selama 10 menit hingga {{ $expiresAt }} WIB.
            </div>
            
            <p style="margin-top: 30px; font-size: 14px; color: #64748b;">
                Jika Anda tidak merasa mendaftar di TokoKu, silakan abaikan email ini dengan aman.
            </p>
        </div>
        <div class="footer">
            <p>Dikirim secara otomatis oleh <strong>Tim TokoKu</strong></p>
            <p>&copy; 2026 TokoKu Store. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
