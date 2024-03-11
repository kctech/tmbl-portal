<?php

namespace App\Console\Commands;

use App\Models\User;
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

        $this->info("dev command"); return;

        /*
        $delete_users = ['Chris Evans','Elaine Swinney','Lisa Stratford','Robert Langley','Julian Girdler','Clare Cowie','John Taylor','Jonathan Saint','Rebecca Dingwall','Katrina Seward','Raj Jandu','Amber Strachan','Tmbl Admin','Jonathan Burridge','Peter Dowling','Rob Thomas','Kevin Appleton','Leila Ouchikh','Sarah Milthorp','Sunny Bhatia','Fiona Wright','Leigh McShane','Mark Wrennall','Steven Saunders','Phil Shearer','Jo Singh','Nigel Holt','Claire Welcher','Roz Higgs','Will Lambe','Glynn Rowe','Alastair Bowser','Kevin Gandy','Catherine Federer','Jack Scarff','Jenny Reeves','Louise Mcnab','Jordan Kay','Claire Harrington','Angela Butler','Nadine Hunter','Louise Mccaffery','Jo Smerdon','Liam Riley','Karl Christopher','Lee Gathercole','Kelly Dyer','Alison Souden','Didier Malo','Heidi Pettigrew','Caroline Bignell','Caroline Raxworthy','Neezam Romjon','Holly Jordan','Act Sales','Mandy Garnish','Oliver Whelan','Nasreen Holmes','Megan Mcknight','Ben Roberts','Gary Boyack','Paul Garvin','Anthony Moore','Mark Travell','Finlay Chandler','Kelly Pollard','Joy Ayres'];
        $system_users = User::all();
        foreach($system_users as $u){
            if(in_array($u->first_name." ".$u->last_name, $delete_users)){
                $u->delete();
            }else{
                $this->info($u->id." - ". $u->first_name." ".$u->last_name);
            }
        }
        return;
        */

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
        $user = $graph->getIdentifierByEmail('sam@tmblgroup.co.uk','startswith');
        dd($user);
        //$users = $graph->getUsers('sam@tmblgroup.co.uk');
        //$users = $graph->getUsers('@tmblgroup.co.uk',['headers'=>['ConsistencyLevel'=>'eventual']]);
        //$users = $graph->getUsersWithPresence();
        //dd($users);

        $calendars = [];
        $fetch_users = [];
        foreach($graph->getUsers() as $user){
            $fetch_users[] = $user->mail;
        }
        //$base_user = (object) ($graph->getUsers('sam@tmblgroup.co.uk')[0] ?? []);
        //foreach(array_chunk($fetch_users,100) as $chunk){
        //    dump($chunk);
        //    $calendars = array_merge($calendars,$graph->getMultipleUserSchedules($base_user->id,$chunk,\Carbon\Carbon::now()->addWeek()->startOfWeek(),\Carbon\Carbon::now()->addWeek()->endOfWeek()));
        //}
        //dump($calendars);
    }
}
