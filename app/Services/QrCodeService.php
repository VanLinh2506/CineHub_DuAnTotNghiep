<?php

namespace App\Services;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
    /**
     * Tạo QR code base64 PNG
     */
    public function generateBase64(string $data, int $size = 300): string
    {
        $writer = new PngWriter();

        $qrCode = QrCode::create($data)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize($size)
            ->setMargin(10)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $result = $writer->write($qrCode);

        return base64_encode($result->getString());
    }

    /**
     * Tạo QR code dạng data URI (dùng trực tiếp trong <img src="">)
     */
    public function generateDataUri(string $data, int $size = 300): string
    {
        $writer = new PngWriter();

        $qrCode = QrCode::create($data)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize($size)
            ->setMargin(10)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $result = $writer->write($qrCode);

        return $result->getDataUri();
    }

    /**
     * Tạo QR code cho vé xem phim
     * Payload: TICKET-{ticket_id}-{seat}-{qr_code_token}
     */
    public function generateTicketQr(int $ticketId, string $seat, string $token, int $size = 280): string
    {
        $data = sprintf('TICKET-%d-%s-%s', $ticketId, $seat, $token);

        return $this->generateDataUri($data, $size);
    }

    /**
     * Tạo QR code cho booking (toàn bộ đơn đặt)
     * Payload: BOOKING-{booking_id}-{qr_code_token}
     */
    public function generateBookingQr(int $bookingId, string $token, int $size = 300): string
    {
        $data = sprintf('BOOKING-%d-%s', $bookingId, $token);

        return $this->generateDataUri($data, $size);
    }
}
