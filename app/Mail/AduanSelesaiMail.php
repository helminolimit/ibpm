<?php

namespace App\Mail;

use App\Models\AduanIct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AduanSelesaiMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly AduanIct $aduan) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Aduan ICT Telah Selesai — '.$this->aduan->no_tiket,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.aduan-selesai',
        );
    }
}
