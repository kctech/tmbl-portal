<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use \App\Models\PortalCache;
use \Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CacheCalendars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal_cache:calendars {--account_id=1} {--base_user_email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild Azure user calendar cache';

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

        $account_id = $this->option('account_id');
        $base_user_email = $this->option('base_user_email');

        $this->info("Attempting Update of calendars for account " .$account_id. " using ".$base_user_email." as a base user");

        //Try and instantiate an Azureprovider instance for teams. It'll throw if there's no account config for it or it's not enabled
        try {
            $azure = new \App\Libraries\SSO\AzureProvider($account_id, 'TEAMS', true);
        } catch (\App\Exceptions\AccountNotConfiguredException $exception) {
            $this->error("No azure support for account: 1");
            return;
        }
        $graph = new \App\Libraries\Azure\GraphConnector($azure);
        $calendars = [];
        $fetch_users = [];
        $users = $graph->getUsers('@tmblgroup.co.uk',['headers'=>['ConsistencyLevel'=>'eventual']]);
        foreach($users as $user){
            $fetch_users[] = $user->mail;
        }
        $this->info("Updating " .count($fetch_users). " user calendars");
        $base_user = (object) ($graph->getUsers($base_user_email,['headers'=>['ConsistencyLevel'=>'eventual']])[0] ?? []);

        if(empty($base_user)){
            $this->error("No azure user found for base user");
            return;
        }

        foreach(array_chunk($fetch_users,100) as $chunk){
            $calendars = array_merge($calendars,$graph->getMultipleUserSchedules($base_user->id,$chunk,\Carbon\Carbon::now()->addWeek()->startOfWeek(),\Carbon\Carbon::now()->addWeek()->endOfWeek()));
        }

        //check calendars fetched = calendars requested - we don't want partials
        if(!empty($calendars)){
            if(count($calendars) == count($fetch_users)){
                session()->put('account_id', ($account_id ?? 0));
                if(PortalCache::updateOrCreate(
                    [
                        'account_id' => $account_id,
                        'cache_key' => 'azure_calendars'
                    ],
                    [
                        'uuid' => Str::uuid(),
                        'data' => json_encode($calendars),
                        'expires_at' => Carbon::now()->addHours(3)->format("Y-m-d H:i:s")
                    ]
                )){
                    $this->info("Calendars updated");
                }else{
                    $this->error("Unable to update cache");
                }
                session()->forget(['account_id']);
            }else{
                $this->error("Cache only partially fetched: ".count($calendars). " / " .count($fetch_users));
            }
        }else{
            $this->error("Calendars empty");
        }
    }
}
