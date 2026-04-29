<?php

namespace App\Mail;

use App\Models\PermohonanPortal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PermohonanPortalDiterima extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PermohonanPortal $permohonan) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permohonan Kemaskini Portal Diterima — IBPM MOTAC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.permohonan_portal_diterima',
        );
    }
}
