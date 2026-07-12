<?php

namespace App\Mail;

use App\Models\TheaterContract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TheaterContract $contract,
        public int $daysLeft
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[CineHub] Hợp đồng {$this->contract->contract_code} sắp hết hạn ({$this->daysLeft} ngày)",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contract-expiring',
        );
    }
}
