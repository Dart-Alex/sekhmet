<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Chan;

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
		if(isset($this->args['chan_id'])) {
			$to = 'au canal '.Chan::where('chan_id', $this->args['chan_id'])->first()->displayName().'.';
		}
		else {
			$to = 'aux administrateurs.';
		}
		return $this
			->replyTo($this->args['from'], $this->args['fromName'])
			->subject('Formulaire de contact '.config('app.name', 'Sekhmet'))
			->markdown('emails.contact')
			->with([
				'fromName' => $this->args['fromName'],
				'to' => $to,
				'from' => $this->args['from'],
				'body' => $this->args['body']
			]);
    }
}
