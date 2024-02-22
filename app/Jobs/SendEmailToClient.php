<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
use App\Mail\SendPDFToClient;

class SendEmailToClient implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
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
        $this->client = $client;
        $this->file2 = $file2;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // var_dump($this->file, $this->subject, $this->name, $this->email);exit;
        if(!empty($this->file2)){
            Mail::to($this->client->email)->send(new SendPDFToClient($this->file, $this->subject, $this->client, $this->file2, $this->type));
        }else{
            Mail::to($this->client->email)->send(new SendPDFToClient($this->file, $this->subject, $this->client, null, $this->type));
        }
    }
}
