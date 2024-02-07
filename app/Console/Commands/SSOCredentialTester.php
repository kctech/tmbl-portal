<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Libraries\Azure\GraphConnector;
use App\Libraries\SSO\AzureProvider;
use App\Libraries\Azure\OnlineMeeting;

use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

class SSOCredentialTester extends Command
{

    public static $threshold_days = 30;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sso_credential_tester {account_id} {client_email_address} {perpetual_email_address=kyle@perpetual.pro}';

    /**§§
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test SSO credentials for validity';

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
        $account_id = $this->argument('account_id');
        $client_email_address = $this->argument('client_email_address');
        $perpetual_email_address = $this->argument('perpetual_email_address');
        $date = Carbon::now();

        if(empty($account_id)){
            dd("invalid account id: ".$account_id);
        }

        if (!is_email($client_email_address)) {
            dd("invalid client email address: " . $client_email_address);
        }

        if (!is_email($perpetual_email_address)) {
            dd("invalid perpetual email address: " . $perpetual_email_address);
        }

        //Try and instantiate an Azureprovider instance for teams. It'll throw if there's no account config for it or it's not enabled
        try {
            $azure = new AzureProvider($account_id, 'TEAMS', true);
        } catch (\App\Exceptions\AccountNotConfiguredException $exception) {
            dd("No teams meeting support for account: " . $account_id);
        }

        $graph = new GraphConnector($azure);

        $users = $graph->getUsers($client_email_address);
        dump($users);

        //TEAMS
        $meeting = new OnlineMeeting($client_email_address);
        $meeting->subject = "Portal TEST - please ignore/cancel";
        $meeting->description = "Testing Azure/Teams API credentials";
        $meeting->date = $date->format("Y-m-d");
        $meeting->time = $date->startOfHour()->addHour()->format("H:i");
        $meeting->duration = 15;
        $meeting->addAttendee("Perpetual Test", $perpetual_email_address);

        $confirmation = $graph->createOnlineMeeting($meeting);

        dump($confirmation);

        switch($confirmation->error){
            case null:
                $err = "NO_ERROR";
                break;
            case 1:
                $err = "USER_NOT_FOUND";
                break;
            case 2:
                $err = "MEETING_CREATION_FAILED";
                break;
            case 3:
                $err = "USER_FETCH_FAILED";
                break;
            case 4:
                $err = "DATA_MISSING";
                break;
            case 8:
                $err = "NO_ATTENDEES";
                break;
            default:
                $err = "NO_ERROR";
        }

        dd($err);
    }
}
