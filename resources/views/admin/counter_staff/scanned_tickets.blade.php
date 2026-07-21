@extends('admin.counter_staff.layout')

@section('content')
<div class="stat-card">
    <h5 class="mb-4"><i class="fas fa-ticket-alt"></i> Vé đã quét hôm nay</h5>

    <div class="mb-3">
        <form method="GET" action="{{ route('counter.scanned') }}" class="d-flex gap-2">
            <input type="date" name="date" class="form-control" value="{{ $date }}" style="max-width: 200px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Xem
            </button>
        </form>
    </div>

    @if($tickets->isEmpty())
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
                        <th>Nước / combo đã giao</th>
                        <th>Thời gian quét</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->bookingPending->qr_code ?? $ticket->bookingPending->id ?? 'N/A' }}</td>
                        <td>{{ $ticket->user->name ?? 'Khách lẻ' }}</td>
                        <td>{{ $ticket->showtime->movie->title ?? 'N/A' }}</td>
                        <td>{{ optional($ticket->showtime)->show_date ? date('d/m/Y H:i', strtotime($ticket->showtime->show_date . ' ' . $ticket->showtime->show_time)) : 'N/A' }}</td>
                        <td>{{ $ticket->showtime->screen->screen_name ?? 'N/A' }}</td>
                        <td>{{ $ticket->seat }}</td>
                        <td>
                            @forelse(($foodByBooking[$ticket->booking_pending_id] ?? collect()) as $item)
                                <span class="badge bg-info text-dark">{{ $item['name'] }} × {{ $item['quantity'] }}</span>
                            @empty
                                <span class="text-muted">Không có</span>
                            @endforelse
                        </td>
                        <td>{{ $ticket->picked_up_at ? date('d/m/Y H:i:s', strtotime($ticket->picked_up_at)) : 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="d-flex justify-content-center">
                {{ $tickets->appends(['date' => $date])->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
