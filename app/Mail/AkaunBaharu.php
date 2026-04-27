<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AkaunBaharu extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Akaun Baharu Telah Didaftarkan — IBPM MOTAC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.akaun_baharu',
        );
    }
}
