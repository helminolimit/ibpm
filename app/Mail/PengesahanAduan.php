<?php

namespace App\Mail;

use App\Models\AduanIct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PengesahanAduan extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly AduanIct $aduan) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengesahan Aduan ICT — '.$this->aduan->no_tiket,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.pengesahan-aduan',
        );
    }
}
