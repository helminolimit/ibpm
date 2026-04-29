<?php

namespace App\Mail;

use App\Models\PermohonanPortal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatusPortalDikemaskini extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PermohonanPortal $permohonan) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[ICTServe] Status Permohonan '.$this->permohonan->no_tiket.' Dikemaskini — IBPM MOTAC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.status_portal_dikemaskini',
        );
    }
}
