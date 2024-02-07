<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\SSOClientCredentials;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Auth;

use Illuminate\Support\Facades\Mail;
use App\Mail\EmailTemplated;
use App\Jobs\QueueEmail;

Use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SSOCredentialExpiryReminder extends Command
{

    public static $threshold_days = 30;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:sso_credential_checker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check SSO credentials for an expiry date and email if getting close';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $credentials = SSOClientCredentials::where('enabled',1)->where(function($q){$q->whereNotNull('expiry')->where('expiry','!=','');})->get();
        foreach($credentials as $credential){
            session()->put('account_id', $credential->account_id);
            if(Carbon::parse($credential->expiry)->diffInDays(Carbon::now()) < self::$threshold_days){
                $data = (object) [
                    'credential' => $credential,
                    'account' => \App\Models\Account::current()
                ];
                $this->info(self::emailReport($data));
            }else{
                $this->info($credential->id. " (". $credential->account_id .") is ok.");
            }
            session()->forget('account_id');
        }

    }

    private static function emailReport($data){
        $emailVars = [
            //'force' => true,
            'data' => $data,
            'to' => ["support@reach-ats.com,development@reach-ats.com"],
            'from' => "noreply@reach-ats.com",
            'fromName' => "Reach SSO Credential Checker",
            'subject' => "IMPORTANT: SSO credentials expiring for account ". $data->credential->account_id,
            'view' => "email.system.sso_credential_expiry"
        ];

        //send email
        try {
            dispatch(new QueueEmail($client))->onQueue('clientemails');
            return __("SUCCESS: Email send reminding for " . $data->credential->id . "(" . $data->credential->account_id . ")");
        } catch (\Exception $e) {
            Log::error($e);
            return __("ERROR: Email failed reminding for " . $data->credential->id . "(" . $data->credential->account_id . ")");
        }

    }
}
