<?php

use Illuminate\Support\Collection;

function buildAdvisersList() : Collection {
    $list = [];
    $users = [];
    $default_status = (object)[
        "availability" => "PresenceUnknown",
        "activity" => "PresenceUnknown",
        "statusMessage" => null
    ];
    $system_users = \App\Models\User::withCount('leads')->where('account_id', session('account_id'))->orderBy('leads_count', 'asc')->get();

    try {
        $azure = new \App\Libraries\SSO\AzureProvider(1, 'TEAMS', true);
    } catch (\App\Exceptions\AccountNotConfiguredException $exception) {
        dd("No teams meeting support for account: 1");
    }
    $graph = new \App\Libraries\Azure\GraphConnector($azure);
    //$users = $graph->getUsers('@tmblgroup.co.uk',['headers'=>['ConsistencyLevel'=>'eventual']]);
    $azure_user_presence = $graph->getUsersWithPresence('@tmblgroup.co.uk',['headers'=>['ConsistencyLevel'=>'eventual']]);

    //Preserve presence order and show Portal users only
    foreach($system_users as $user){
        $users[$user->email] = $user;
    }
    foreach($azure_user_presence as $email => $status){
        if(isset($users[$email])){
            $users[$email]->presence = $status ?? $default_status;
            $list[] = $users[$email];
        }
    }
    return collect($list);
}
