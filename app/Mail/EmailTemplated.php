<?php

namespace App\Mail;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\PdfController;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailTemplated extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $ident = "Generic Templated Email";

    /**
     * Create a new message instance.
     *
     * @param $details
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;

        if (isset($this->details["ident"])) {
            $this->ident = $this->details["ident"];
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->view($this->details['view'])
            ->from('themortgagebroker@tmblgroup.co.uk')
            ->subject($this->details['subject']);

        if (isset($this->details['from'])) {
            if (!empty($this->details['from'])) {
                $email->from($this->details['from'], $this->details['fromName']);
            } else {
                $email->from('themortgagebroker@tmblgroup.co.uk');
            }
        } else {
            $email->from('themortgagebroker@tmblgroup.co.uk');
        }

        if (isset($this->details['replyTo'])) {
            if (!empty($this->details['replyTo'])) {
                $email->replyTo($this->details['replyTo']);
            }
        }

        if (isset($this->details['attachments'])) {
            if (!empty($this->details['attachments'])) {
                foreach ($this->details['attachments'] as $attachment) {
                    $file = 'none';
                    //check file exists, else make it
                    if (Storage::disk($attachment['disk'])->exists($attachment['file'])) {
                        $file = 'saved';
                    } elseif (!empty($attachment['view'])) {
                        $file = PdfController::generatePDF($this->details['fields'], $attachment['view'], $attachment['file'], 'save');
                        //check it's been made
                        if (Storage::disk($attachment['disk'])->exists($attachment['file'])) {
                            $file = 'saved';
                        }
                    }

                    //attach
                    if ($file == 'saved') {
                        $email->attachFromStorageDisk($attachment['disk'], $attachment['file']);
                    }
                }
            }
        }

        return $email;
    }
}
