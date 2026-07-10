<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mã OTP xác nhận đăng ký tài khoản</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #e1e8ed;
        }
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 30px 40px;
            line-height: 1.6;
        }
        .content p {
            margin: 0 0 20px;
            font-size: 16px;
            color: #555555;
        }
        .otp-box {
            background-color: #f0f4f8;
            border: 2px dashed #2a5298;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #1e3c72;
            margin: 0;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #888888;
            border-top: 1px solid #eef2f5;
        }
        .footer p {
            margin: 5px 0;
        }
        .highlight {
            font-weight: 600;
            color: #e03131;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CineHub - Xác Nhận Đăng Ký</h1>
        </div>
        <div class="content">
            <p>Xin chào,</p>
            <p>Cảm ơn bạn đã lựa chọn đăng ký tài khoản tại <strong>CineHub</strong>. Để hoàn tất quá trình đăng ký, vui lòng sử dụng mã xác thực OTP dưới đây:</p>
            
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
            </div>
            
            <p>Mã OTP này có hiệu lực trong vòng <span class="highlight">5 phút</span>. Vui lòng không chia sẻ mã này với bất kỳ ai để bảo vệ tài khoản của bạn.</p>
            <p>Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này.</p>
            <p>Trân trọng,<br>Đội ngũ CineHub</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} CineHub. All rights reserved.</p>
            <p>Đây là email tự động, vui lòng không phản hồi email này.</p>
        </div>
    </div>
</body>
</html>
