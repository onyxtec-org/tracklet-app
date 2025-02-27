<?php

namespace App\Mail;

use App\Models\DeviceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeviceRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $deviceRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(DeviceRequest $deviceRequest)
    {
        $this->deviceRequest = $deviceRequest;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Device Request has been Submitted')
                    ->view('emails.device_request_submitted')
                    ->with(['deviceRequest' => $this->deviceRequest]);
    }
}

