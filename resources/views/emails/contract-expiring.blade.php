<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f6fa;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #4a3567 0%, #8b0000 100%);
            padding: 30px;
            text-align: center;
            color: #fff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
        }
        .header .subtitle {
            margin-top: 8px;
            font-size: 14px;
            opacity: 0.85;
        }
        .content {
            padding: 30px;
        }
        .alert-box {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
            border-left: 4px solid #ffc107;
            padding: 18px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        .alert-box.danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border-left-color: #dc3545;
        }
        .alert-box h3 {
            margin: 0 0 8px;
            color: #856404;
            font-size: 16px;
        }
        .alert-box.danger h3 {
            color: #721c24;
        }
        .alert-box p {
            margin: 0;
            color: #555;
            font-size: 14px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .info-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        .info-table td:first-child {
            color: #888;
            width: 40%;
            font-weight: 500;
        }
        .info-table td:last-child {
            color: #333;
            font-weight: 600;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #fff;
        }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-danger { background: #dc3545; }
        .badge-success { background: #28a745; }
        .cta-section {
            text-align: center;
            margin: 24px 0;
        }
        .cta-btn {
            display: inline-block;
            padding: 14px 36px;
            background: linear-gradient(135deg, #4a3567 0%, #8b0000 100%);
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #999;
            font-size: 12px;
            border-top: 1px solid #eee;
        }
        .countdown {
            font-size: 42px;
            font-weight: 800;
            text-align: center;
            margin: 16px 0;
        }
        .countdown .days { color: {{ $daysLeft <= 3 ? '#dc3545' : '#ffc107' }}; }
        .countdown .label { font-size: 14px; color: #888; font-weight: 400; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎬 CINEHUB</h1>
            <div class="subtitle">Hệ thống quản lý rạp chiếu phim</div>
        </div>

        <div class="content">
            <div class="alert-box {{ $daysLeft <= 3 ? 'danger' : '' }}">
                <h3>⚠️ Hợp đồng sắp hết hạn!</h3>
                <p>Hợp đồng quản lý rạp của bạn sắp hết hiệu lực. Vui lòng liên hệ Super Admin để gia hạn.</p>
            </div>

            <div class="countdown">
                <div class="days">{{ $daysLeft }}</div>
                <div class="label">ngày còn lại</div>
            </div>

            <table class="info-table">
                <tr>
                    <td>Mã hợp đồng</td>
                    <td>{{ $contract->contract_code }}</td>
                </tr>
                <tr>
                    <td>Tên rạp</td>
                    <td>{{ $contract->theater->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Đại diện rạp</td>
                    <td>{{ $contract->representative->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Ngày bắt đầu</td>
                    <td>{{ $contract->start_date->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td>Ngày kết thúc</td>
                    <td>{{ $contract->end_date->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td>Trạng thái</td>
                    <td><span class="badge badge-warning">Sắp hết hạn</span></td>
                </tr>
            </table>

            <div class="cta-section">
                <a href="{{ url('/admin/contracts/' . $contract->id) }}" class="cta-btn">
                    📋 Xem chi tiết & Gia hạn
                </a>
            </div>

            <p style="font-size: 13px; color: #888; text-align: center;">
                Nếu hợp đồng không được gia hạn trước <strong>{{ $contract->end_date->format('d/m/Y') }}</strong>,
                hệ thống sẽ tự động thu hồi quyền Admin rạp.
            </p>
        </div>

        <div class="footer">
            Email tự động từ hệ thống CineHub — {{ now()->format('d/m/Y H:i') }}<br>
            Vui lòng không trả lời email này.
        </div>
    </div>
</body>
</html>
