<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NovaReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public $review;

    /**
     * Create a new message instance.
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Nova review submetida na Biblioteca')
            ->markdown('emails.nova-review');
    }
}
