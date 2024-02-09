<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Dev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dev command';

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
        //$mab = new \App\Libraries\MABApi(false,'introducers:read:authorizedfirms',true);
        //dd($mab->getAdvisers());
        //dd($mab->getAdviser("Marc Finch"));


        //Try and instantiate an Azureprovider instance for teams. It'll throw if there's no account config for it or it's not enabled
        try {
            $azure = new \App\Libraries\SSO\AzureProvider(1, 'TEAMS', true);
        } catch (\App\Exceptions\AccountNotConfiguredException $exception) {
            dd("No teams meeting support for account: 1");
        }
        $graph = new \App\Libraries\Azure\GraphConnector($azure);
        //$users = $graph->getUsers('sam@tmblgroup.co.uk');
        //$users = $graph->getUsers();
        //$users = $graph->getUsersWithPresence();

        $calendars = [];
        $fetch_users = [];
        foreach($graph->getUsers() as $user){
            $fetch_users[] = $user->mail;
        }
        $base_user = (object) ($graph->getUsers('sam@tmblgroup.co.uk')[0] ?? []);
        foreach(array_chunk($fetch_users,100) as $chunk){
            dump($chunk);
            $calendars = array_merge($calendars,$graph->getMultipleUserSchedules($base_user->id,$chunk,\Carbon\Carbon::now()->addWeek()->startOfWeek(),\Carbon\Carbon::now()->addWeek()->endOfWeek()));
        }
        dump($calendars);
    }
}
