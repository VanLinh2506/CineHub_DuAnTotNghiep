<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Vé Phim - {{ ($ticket ?? null)?->qr_code ?: ($booking->qr_code ?: $booking->id) }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .ticket-wrapper {
            border: 2px dashed #ccc;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #e50914;
            font-size: 24px;
        }
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 18px;
        }
        .content table {
            width: 100%;
            border-collapse: collapse;
        }
        .content th, .content td {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .content th {
            text-align: left;
            width: 30%;
            color: #666;
        }
        .qr-section {
            text-align: center;
            margin-top: 30px;
        }
        .qr-section img {
            width: 150px;
            height: 150px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
        .food-items {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    @php
        $showtime = $booking->showtime;
        $movie = $showtime->movie ?? null;
        $theater = $showtime->screen->theater ?? null;
        $screen = $showtime->screen ?? null;
        $ticket = $ticket ?? null;
        $seats = $ticket ? [$ticket->seat] : $booking->tickets->pluck('seat')->toArray();
        $foodItems = $foodItems ?? collect();
        $totalPrice = $ticket ? (float) $ticket->price : $booking->tickets->sum('price');
        $totalPrice += $foodItems->sum('subtotal');
        $ticketCode = $ticket?->qr_code ?: ($booking->qr_code ?: ('BOOKING-' . $booking->id));
    @endphp

    <div class="ticket-wrapper">
        <div class="header">
            <h1>CINEHUB TICKET</h1>
            <h2>{{ $movie->title ?? 'N/A' }}</h2>
        </div>
        
        <div class="content">
            <table>
                <tr>
                    <th>Mã đặt vé:</th>
                    <td><strong>{{ $ticketCode }}</strong></td>
                </tr>
                <tr>
                    <th>Rạp:</th>
                    <td>{{ $theater->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Phòng chiếu:</th>
                    <td>{{ $screen->screen_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Thời gian:</th>
                    <td>{{ $showtime->show_time ?? 'N/A' }} - {{ $showtime->show_date ? date('d/m/Y', strtotime($showtime->show_date)) : 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Ghế:</th>
                    <td><strong>{{ implode(', ', $seats) }}</strong></td>
                </tr>
                @if($foodItems->isNotEmpty())
                <tr>
                    <th>Đồ ăn/uống:</th>
                    <td>
                        @foreach($foodItems as $food)
                            {{ $food['name'] }} (x{{ $food['quantity'] }})<br>
                        @endforeach
                    </td>
                </tr>
                @endif
                <tr>
                    <th>Tổng tiền:</th>
                    <td><strong>{{ number_format($totalPrice, 0, ',', '.') }} VNĐ</strong></td>
                </tr>
                <tr>
                    <th>Khách hàng:</th>
                    <td>{{ $booking->user->name ?? 'Khách' }} ({{ $booking->customer_email }})</td>
                </tr>
            </table>
        </div>

        <div class="qr-section">
            <img src="{{ qr_code_data_uri($ticketCode, 200) }}" alt="QR Code">
            <p>Vui lòng xuất trình mã QR này cho nhân viên rạp</p>
        </div>

        <div class="footer">
            <p>Cảm ơn bạn đã lựa chọn CineHub!</p>
            <p>Vé chỉ có giá trị cho suất chiếu trên. Vui lòng đến trước 15 phút.</p>
        </div>
    </div>
</body>
</html>
