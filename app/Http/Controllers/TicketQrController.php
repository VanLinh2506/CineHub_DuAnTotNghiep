<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Ticket;
use App\Services\QrCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TicketQrController extends Controller
{
    public function __construct(protected QrCodeService $qrCode) {}

    /**
     * Xuất QR cho toàn bộ booking (1 ảnh đại diện cho cả đơn)
     * GET /api/bookings/{bookingId}/qr
     */
    public function bookingQr(int $bookingId): JsonResponse
    {
        $booking = Booking::with(['showtime.movie', 'showtime.theater', 'tickets'])
            ->where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status !== 'completed') {
            return response()->json(['message' => 'Booking chưa thanh toán'], 422);
        }

        $qrDataUri = $this->qrCode->generateBookingQr($booking->id, $booking->qr_code);

        return response()->json([
            'booking_id'   => $booking->id,
            'movie'        => $booking->showtime->movie->title,
            'theater'      => $booking->showtime->theater->name,
            'show_date'    => $booking->showtime->show_date,
            'show_time'    => $booking->showtime->show_time,
            'seats'        => $booking->seats,
            'total_amount' => $booking->total_amount,
            'qr_image'     => $qrDataUri,  // data:image/png;base64,...
        ]);
    }

    /**
     * Xuất QR riêng cho từng vé (từng ghế)
     * GET /api/tickets/{ticketId}/qr
     */
    public function ticketQr(int $ticketId): JsonResponse
    {
        $ticket = Ticket::with(['showtime.movie', 'showtime.theater'])
            ->where('id', $ticketId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($ticket->status !== 'Đã đặt') {
            return response()->json(['message' => 'Vé không hợp lệ'], 422);
        }

        $qrDataUri = $this->qrCode->generateTicketQr($ticket->id, $ticket->seat, $ticket->qr_code);

        return response()->json([
            'ticket_id'  => $ticket->id,
            'movie'      => $ticket->showtime->movie->title,
            'theater'    => $ticket->showtime->theater->name,
            'show_date'  => $ticket->showtime->show_date,
            'show_time'  => $ticket->showtime->show_time,
            'seat'       => $ticket->seat,
            'seat_type'  => $ticket->seat_type,
            'price'      => $ticket->price,
            'qr_image'   => $qrDataUri,  // data:image/png;base64,...
        ]);
    }

    /**
     * Xuất tất cả QR vé theo booking (mỗi ghế 1 QR)
     * GET /api/bookings/{bookingId}/tickets/qr
     */
    public function allTicketsQr(int $bookingId): JsonResponse
    {
        $booking = Booking::with(['showtime.movie', 'showtime.theater', 'tickets'])
            ->where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status !== 'completed') {
            return response()->json(['message' => 'Booking chưa thanh toán'], 422);
        }

        $tickets = $booking->tickets->map(function ($ticket) {
            return [
                'ticket_id' => $ticket->id,
                'seat'      => $ticket->seat,
                'seat_type' => $ticket->seat_type,
                'price'     => $ticket->price,
                'qr_image'  => $this->qrCode->generateTicketQr($ticket->id, $ticket->seat, $ticket->qr_code),
            ];
        });

        return response()->json([
            'booking_id' => $booking->id,
            'movie'      => $booking->showtime->movie->title,
            'theater'    => $booking->showtime->theater->name,
            'show_date'  => $booking->showtime->show_date,
            'show_time'  => $booking->showtime->show_time,
            'tickets'    => $tickets,
        ]);
    }
}
