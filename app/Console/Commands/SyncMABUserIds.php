<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use \App\Models\User;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SyncMABUserIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mab:sync-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync MAB users with portal';

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
        $mab = new \App\Libraries\MABApi(false,'introducers:read:authorizedfirms',true);
        //dd($mab->getAdvisers());
        foreach(User::all() as $user){
            $mab_id = $mab->getAdviser($user->full_name());
            if(!is_null($mab_id)){
                $user->mab_id = $mab_id;
                $user->save();
                dump($mab_id);
            }else{
                dump($user->email);
            }
        }
    }
}
