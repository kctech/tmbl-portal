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
    protected $signature = 'mab:sync-users {--refresh=false} {--branch-id=}';

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

        $refresh = false;
        if($this->option('refresh') == 'true'){
            $refresh = true;
        }

        $branch_id = null;
        if(!empty($this->option('branch-id'))){
            $branch_id = $this->option('branch-id');
        }

        $failed = $success = [];
        $mab = new \App\Libraries\MABApi(false,'introducers:read:authorizedfirms',true);
        //dd($mab->getAdvisers());
        foreach(User::all() as $user){
            $mab_id = $mab->getAdviser($user->full_name(), $branch_id);
            if(!is_null($mab_id) || $refresh){
                $user->timestamps = false;
                $user->mab_id = $mab_id;
                $user->save();
                //dump($mab_id);
                $success[$user->email] = $user->full_name();
            }else{
                //dump($user->email);
                $failed[$user->email] = $user->full_name();
            }
        }

        dump("success",json_encode($success));
        dump("failed",json_encode($failed));
    }
}
