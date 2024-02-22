<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPDFToClient extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $file;
    public $subject;
    public $client;
    public $file2;
    public $type;
    public function __construct($file, $subject, $client, $file2 = null, $type)
    {
        $this->file = $file;
        $this->subject = $subject;
        $this->file2 = $file2;
        $this->client = $client;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->view('Mail.sendEmailToClient')->with([
            'type'      =>  $this->type,
            'client'    =>  $this->client
        ])->from('remembrance@methodist.org.sg', $this->subject);
        if(!empty($this->file2)){
            $mail->attach($this->file)->attach($this->file2);
        }else{
            $mail->attach($this->file);
        }
        return $mail;
    }
}
