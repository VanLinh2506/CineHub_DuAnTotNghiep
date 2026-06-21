<?php

use App\Services\QrCodeService;

if (!function_exists('generate_qr_code')) {
    /**
     * Generate QR code
     *
     * @param string $data
     * @param string|null $label
     * @param int $size
     * @return string Base64 encoded PNG
     */
    function generate_qr_code(string $data, ?string $label = null, int $size = 300): string
    {
        $service = app(QrCodeService::class);
        return $service->generateTicketQrCode($data, $label, $size);
    }
}

if (!function_exists('qr_code_data_uri')) {
    /**
     * Generate QR code as data URI
     *
     * @param string $data
     * @param int $size
     * @return string Data URI
     */
    function qr_code_data_uri(string $data, int $size = 300): string
    {
        $service = app(QrCodeService::class);
        return $service->generateDataUri($data, $size);
    }
}

if (!function_exists('generate_ticket_qr')) {
    /**
     * Generate QR code for ticket
     *
     * @param int $ticketId
     * @param string $seatNumber
     * @param string $verificationCode
     * @return string Base64 encoded PNG
     */
    function generate_ticket_qr(int $ticketId, string $seatNumber, string $verificationCode): string
    {
        $service = app(QrCodeService::class);
        return $service->generateTicketVerificationQrCode($ticketId, $seatNumber, $verificationCode);
    }
}

if (!function_exists('generate_booking_qr')) {
    /**
     * Generate QR code for booking
     *
     * @param int $bookingId
     * @param string $verificationCode
     * @return string Base64 encoded PNG
     */
    function generate_booking_qr(int $bookingId, string $verificationCode): string
    {
        $service = app(QrCodeService::class);
        return $service->generateBookingQrCode($bookingId, $verificationCode);
    }
}

if (!function_exists('movie_img')) {
    /**
     * Trả về URL ảnh phim đúng cách.
     * - Nếu đã là URL đầy đủ (http/https) → giữ nguyên
     * - Nếu là path tương đối → dùng asset()
     * - Nếu rỗng → trả về placeholder
     */
    function movie_img(?string $path, string $placeholder = ''): string
    {
        if (empty($path)) {
            return $placeholder ?: asset('data/img/placeholder.svg');
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return storage_url($path);
    }
}
