@extends('admin.counter_staff.layout')

@section('content')
<div class="stat-card">
    <h5 class="mb-4"><i class="fas fa-ticket-alt"></i> Vé đã quét hôm nay</h5>

    <div class="mb-3">
        <form method="GET" class="d-flex gap-2">
            <input type="hidden" name="route" value="counterStaff/scannedTickets">
            <input type="date" name="date" class="form-control" value="{{ $date }}" style="max-width: 200px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Xem
            </button>
        </form>
    </div>

    @if(empty($tickets))
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Chưa có vé nào được quét trong ngày này.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Booking Code</th>
                        <th>Khách hàng</th>
                        <th>Phim</th>
                        <th>Ngày chiếu</th>
                        <th>Phòng</th>
                        <th>Ghế</th>
                        <th>Thời gian quét</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket['booking_code'] ?? 'N/A' }}</td>
                        <td>{{ $ticket['user_name'] }}</td>
                        <td>{{ $ticket['movie_title'] }}</td>
                        <td>{{ date('d/m/Y H:i', strtotime($ticket['show_date'] . ' ' . $ticket['show_time'])) }}</td>
                        <td>{{ $ticket['screen_name'] }}</td>
                        <td>{{ $ticket['seat'] }}</td>
                        <td>{{ date('d/m/Y H:i:s', strtotime($ticket['picked_up_at'])) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($total_pages > 1)
            <nav>
                <ul class="pagination justify-content-center">
                    @for($i = 1; $i <= $total_pages; $i++)
                        <li class="page-item {{ $i == $page ? 'active' : '' }}">
                            <a class="page-link" href="?route=counterStaff/scannedTickets&page={{ $i }}&date={{ urlencode($date) }}">{{ $i }}</a>
                        </li>
                    @endfor
                </ul>
            </nav>
        @endif
    @endif
</div>
@endsection
