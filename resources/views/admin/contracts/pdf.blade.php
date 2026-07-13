<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #222;
            font-size: 12px;
            line-height: 1.55;
        }
        .header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #8b0000;
            padding-bottom: 14px;
            margin-bottom: 22px;
        }
        .logo, .system {
            display: table-cell;
            vertical-align: middle;
        }
        .logo-box {
            width: 86px;
            height: 58px;
            border: 2px solid #8b0000;
            color: #8b0000;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            line-height: 58px;
        }
        .logo img {
            max-width: 96px;
            max-height: 64px;
        }
        .system {
            text-align: right;
        }
        h1 {
            text-align: center;
            font-size: 22px;
            margin: 8px 0 4px;
            text-transform: uppercase;
        }
        .subtitle {
            text-align: center;
            color: #555;
            margin-bottom: 26px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            border: 1px solid #ddd;
            padding: 9px;
        }
        .info-table td:first-child {
            width: 32%;
            background: #f5f5f5;
            font-weight: bold;
        }
        .section-title {
            font-weight: bold;
            color: #8b0000;
            margin: 18px 0 8px;
            text-transform: uppercase;
        }
        ul {
            margin-top: 6px;
        }
        .terms {
            border: 1px solid #ddd;
            padding: 12px;
            background: #fafafa;
        }
        .signatures {
            display: table;
            width: 100%;
            margin-top: 46px;
        }
        .signature {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        .signature-name {
            margin-top: 66px;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            border-top: 1px solid #ddd;
            padding-top: 6px;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            @if($logoPath)
                <img src="{{ $logoPath }}" alt="CineHub">
            @else
                <div class="logo-box">CineHub</div>
            @endif
        </div>
        <div class="system">
            <strong>HỆ THỐNG CINEHUB</strong><br>
            Hợp đồng điện tử quản lý rạp
        </div>
    </div>

    <h1>Hợp đồng quản lý rạp</h1>
    <div class="subtitle">Giữa Super Admin CineHub và Đại diện rạp</div>

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
            <td>{{ $contract->representative->name ?? 'N/A' }} - {{ $contract->representative->email ?? '' }}</td>
        </tr>
        <tr>
            <td>Ngày bắt đầu</td>
            <td>{{ $contract->start_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>Ngày kết thúc</td>
            <td>{{ $contract->end_date->format('d/m/Y') }}</td>
        </tr>
    </table>

    <div class="section-title">Bảng giá niêm yết cho suất chiếu</div>
    <table class="info-table">
        <tr><td>Phim bán chạy</td><td>{{ number_format($contract->bestseller_price_min) }} - {{ number_format($contract->bestseller_price_max) }} VNĐ/vé</td></tr>
        <tr><td>Phim mới phát hành</td><td>{{ number_format($contract->new_release_price_min) }} - {{ number_format($contract->new_release_price_max) }} VNĐ/vé</td></tr>
    </table>

    <div class="section-title">Quyền của Admin rạp</div>
    <ul>
        @foreach($contract->admin_permissions ?: [] as $permission)
            <li>{{ $permission }}</li>
        @endforeach
    </ul>

    <div class="section-title">Điều khoản tự động thu hồi quyền khi hết hạn</div>
    <div class="terms">
        {{ $contract->auto_revoke_terms }}
    </div>

    <div class="signatures">
        <div class="signature">
            <strong>Chữ ký Super Admin</strong>
            <div class="signature-name">{{ $contract->super_admin_signature ?: ($contract->superAdmin->name ?? '') }}</div>
        </div>
        <div class="signature">
            <strong>Chữ ký Đại diện rạp</strong>
            <div class="signature-name">{{ $contract->representative_signature ?: ($contract->representative->name ?? '') }}</div>
        </div>
    </div>

    <div class="footer">
        PDF được sinh tự động từ dữ liệu hợp đồng trong database CineHub vào {{ now()->format('d/m/Y H:i') }}.
    </div>
</body>
</html>
