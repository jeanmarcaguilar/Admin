<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $recipientName;
    public string $code;

    /**
     * Create a new message instance.
     */
    public function __construct(string $recipientName, string $code)
    {
        $this->recipientName = $recipientName;
        $this->code = $code;
    }

    /**
     * Build the message.
     */
    public function build()
    {
<<<<<<< HEAD
        return $this->from(config('mail.from.address'), config('mail.from.name'))
=======
        return $this->from('admnistrative22@gmail.com', config('mail.from.name'))
>>>>>>> 3467a8cdf3aef1c3632815755eba1f09b252a719
            ->subject('Your Verification Code')
            ->view('emails.two-factor-code')
            ->with([
                'name' => $this->recipientName,
                'code' => $this->code,
            ]);
    }
}
