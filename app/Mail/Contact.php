<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Contact extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
	protected $args;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($args)
    {
        $this->args = $args;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		return $this
			->replyTo($this->args['from'], $this->args['fromName'])
			->subject('Formulaire de contact '.config('app.name', 'Sekhmet'))
			->markdown('emails.contact')
			->with([
				'fromName' => $this->args['fromName'],
				'from' => $this->args['from'],
				'body' => $this->args['body']
			]);
    }
}
