<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewRegisteredAccount extends Mailable
{
    use Queueable, SerializesModels;

    private $data = [];
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build(){
        return $this->from('ryan.villarma.pixel8@gmail.com',"Registration")
        ->subject($this->data['subject'])->view('email.index')->with('data',$this->data);
    }

}
