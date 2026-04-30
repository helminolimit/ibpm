<?php

namespace App\Mail;

use App\Models\TugasanPortal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TugasanPortalBaru extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public TugasanPortal $tugasan) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[ICTServe] Tugasan Baru — '.$this->tugasan->permohonan->no_tiket,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tugasan_portal_baru',
        );
    }
}
