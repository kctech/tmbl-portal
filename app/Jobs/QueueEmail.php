<?php

//run Queue: php artisan queue:work --queue=adviseremails,clientemails --tries=1

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Mail\EmailTemplated as EmailTemplated;
use Mail;

class QueueEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;

    /**
     * Sendto constructor.
     * @param $details
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        Mail::to($this->details['to'])
            ->send(new EmailTemplated($this->details));
    }
}
