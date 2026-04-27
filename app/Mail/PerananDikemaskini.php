<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PerananDikemaskini extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Peranan Akaun Anda Telah Dikemaskini — IBPM MOTAC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.peranan_kemaskini',
        );
    }
}
