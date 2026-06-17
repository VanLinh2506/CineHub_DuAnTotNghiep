<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffTicketController extends Controller
{
    /**
     * Danh sách vé + tìm kiếm
     * GET /staff/tickets
     */
    public function index(Request $request)
    {
        $theaterId = Auth::user()->theater_id;
        $search    = $request->input('search');
        $date      = $request->input('date', today()->toDateString());
        $status    = $request->input('status');

        $tickets = Ticket::with(['showtime.movie', 'showtime.screen', 'showtime.theater', 'user'])
            ->whereHas('showtime.screen', fn($q) => $q->where('theater_id', $theaterId))
            ->whereHas('showtime', fn($q) => $q->whereDate('show_date', $date))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('seat', 'like', "%{$search}%")
                          ->orWhere('qr_code', 'like', "%{$search}%")
                          ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderBy('showtime_id')
            ->orderBy('seat')
            ->paginate(30)
            ->withQueryString();

        $showtimes = Showtime::with('movie')
            ->whereHas('screen', fn($q) => $q->where('theater_id', $theaterId))
            ->whereDate('show_date', $date)
            ->orderBy('show_time')
            ->get();

        return view('staff.tickets.index', compact('tickets', 'showtimes', 'search', 'date', 'status'));
    }

    /**
     * Check-in vé bằng QR code hoặc mã vé
     * POST /staff/tickets/checkin
     */
    public function checkIn(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $theaterId = Auth::user()->theater_id;
        $code      = trim($request->input('code'));

        // Tìm theo qr_code
        $ticket = Ticket::with(['showtime.screen.theater', 'showtime.movie', 'user'])
            ->where('qr_code', $code)
            ->first();

        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy vé.']);
        }

        // Kiểm tra rạp
        if ($ticket->showtime->screen->theater_id !== $theaterId) {
            return response()->json(['success' => false, 'message' => 'Vé không thuộc rạp của bạn.']);
        }

        // Kiểm tra trạng thái vé
        if ($ticket->status !== 'Đã đặt') {
            return response()->json(['success' => false, 'message' => 'Vé không hợp lệ (trạng thái: ' . $ticket->status . ').']);
        }

        // Đã check-in rồi
        if ($ticket->is_picked_up) {
            return response()->json([
                'success' => false,
                'message' => 'Vé này đã được check-in lúc ' . $ticket->picked_up_at?->format('H:i d/m/Y') . '.',
                'ticket'  => $this->ticketInfo($ticket),
            ]);
        }

        // Thực hiện check-in
        $ticket->update([
            'is_picked_up' => true,
            'picked_up_at' => now(),
            'picked_up_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in thành công!',
            'ticket'  => $this->ticketInfo($ticket),
        ]);
    }

    /**
     * Trang giao diện quét check-in
     * GET /staff/tickets/scan
     */
    public function scan()
    {
        return view('staff.tickets.scan');
    }

    private function ticketInfo(Ticket $ticket): array
    {
        return [
            'id'         => $ticket->id,
            'seat'       => $ticket->seat,
            'seat_type'  => $ticket->seat_type,
            'movie'      => $ticket->showtime->movie->title,
            'show_time'  => substr($ticket->showtime->show_time, 0, 5),
            'show_date'  => $ticket->showtime->show_date instanceof \Carbon\Carbon
                                ? $ticket->showtime->show_date->format('d/m/Y')
                                : \Carbon\Carbon::parse($ticket->showtime->show_date)->format('d/m/Y'),
            'customer'   => $ticket->user?->name ?? 'Khách lẻ',
            'checked_in' => $ticket->is_picked_up,
        ];
    }
}
