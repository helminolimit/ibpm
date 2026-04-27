<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AkaunTidakAktif extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Akaun Anda Telah Dinyahaktifkan — IBPM MOTAC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.akaun_tidak_aktif',
        );
    }
}
