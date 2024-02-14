<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use \App\Models\User;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SyncADUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'azure:sync-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Azure AD user with portal';

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
        //Try and instantiate an Azureprovider instance for teams. It'll throw if there's no account config for it or it's not enabled
        try {
            $azure = new \App\Libraries\SSO\AzureProvider(1, 'TEAMS', true);
        } catch (\App\Exceptions\AccountNotConfiguredException $exception) {
            dd("No teams meeting support for account: 1");
        }
        $graph = new \App\Libraries\Azure\GraphConnector($azure);
        $users = $graph->getUsers('@tmblgroup.co.uk',['headers'=>['ConsistencyLevel'=>'eventual']]);
        foreach($users as $user){
            $ad = User::where('email','LIKE',$user->mail)->withTrashed()->first();
            if($ad){
                $ad->azure_id = $user->id;
                $ad->save();
                dump($ad->azure_id);
            }else{
                //dump($user->mail);
                $name_parts = explode(".",str_replace('@tmblgroup.co.uk','',$user->mail));
                if(isset($name_parts[1])){
                    $new = new User;
                    $new->account_id = 1;
                    $new->role_id = 5;
                    $new->first_name = ucfirst($name_parts[0]);
                    $new->last_name = ucfirst($name_parts[1]);
                    $new->email = $user->mail;
                    $new->password = Hash::make(Str::uuid());
                    $new->email_verified_at = date("Y-m-d H:i:s");
                    $new->azure_id = $user->id;
                    //dump($new);
                    if($new->save()){
                        dump($user->mail);
                    }
                }else{
                    dump($name_parts);
                }
            }

        }
    }
}
