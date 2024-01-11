<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Log;

class NewRegisteredAccount extends Mailable
{
    use Queueable, SerializesModels;

    private $data = [];
    public function __construct(array $data)
    {
        $this->data = $data;
        Log::error("data",$data);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('ryan.villarma.pixel8@gmail.com', 'Registration'),
            subject: $this->data['subject'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.index',
            with:[
                'data'=> $this->data
            ]
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->data['qrpath'])
            ->as($this->data['qrname'])
            ->withMime('image/png'),
        ];
    }



}
