<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
    /**
     * Generate QR code for ticket
     *
     * @param string $data - Ticket data (e.g., ticket ID, booking code)
     * @param string|null $label - Optional label below QR code
     * @param int $size - QR code size in pixels
     * @return string Base64 encoded PNG image
     */
    public function generateTicketQrCode(string $data, ?string $label = null, int $size = 300): string
    {
        $builder = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($data)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size($size)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin());

        if ($label) {
            $builder->labelText($label)
                ->labelAlignment(new LabelAlignment(LabelAlignment::CENTER));
        }

        $result = $builder->build();

        return base64_encode($result->getString());
    }

    /**
     * Generate QR code and save to file
     *
     * @param string $data
     * @param string $filePath - Full path to save file
     * @param string|null $label
     * @param int $size
     * @return bool
     */
    public function generateAndSave(string $data, string $filePath, ?string $label = null, int $size = 300): bool
    {
        $builder = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($data)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size($size)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin());

        if ($label) {
            $builder->labelText($label)
                ->labelAlignment(new LabelAlignment(LabelAlignment::CENTER));
        }

        $result = $builder->build();
        $result->saveToFile($filePath);

        return file_exists($filePath);
    }

    /**
     * Generate simple QR code data URI for embedding in HTML
     *
     * @param string $data
     * @param int $size
     * @return string Data URI (data:image/png;base64,...)
     */
    public function generateDataUri(string $data, int $size = 300): string
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($data)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size($size)
            ->margin(10)
            ->build();

        return $result->getDataUri();
    }

    /**
     * Generate QR code for booking verification
     * Format: BOOKING-{booking_id}-{verification_code}
     *
     * @param int $bookingId
     * @param string $verificationCode
     * @return string Base64 encoded PNG
     */
    public function generateBookingQrCode(int $bookingId, string $verificationCode): string
    {
        $data = sprintf('BOOKING-%d-%s', $bookingId, $verificationCode);
        $label = "Booking #$bookingId";
        
        return $this->generateTicketQrCode($data, $label);
    }

    /**
     * Generate QR code for ticket verification
     * Format: TICKET-{ticket_id}-{seat}-{verification_code}
     *
     * @param int $ticketId
     * @param string $seatNumber
     * @param string $verificationCode
     * @return string Base64 encoded PNG
     */
    public function generateTicketVerificationQrCode(int $ticketId, string $seatNumber, string $verificationCode): string
    {
        $data = sprintf('TICKET-%d-%s-%s', $ticketId, $seatNumber, $verificationCode);
        $label = "Seat: $seatNumber";
        
        return $this->generateTicketQrCode($data, $label, 250);
    }
}
