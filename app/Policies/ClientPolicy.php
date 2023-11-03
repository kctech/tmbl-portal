<?php

namespace App\Policies;

use App\Models\User;
use App\Client;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Session;

class ClientPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can do anything
     *
     * @param  \App\Models\User  $user
     * @param  \App\Client  $client
     * @return mixed
     */
    public function anything(User $user, Client $client)
    {
        //$user->account->modules;
        //return $client->user_id == $user->id;
        $user_id = Session::get('user_id',$user->id);
        return $client->user_id == $user_id;
    }

    /**
     * Determine whether the user can view client
     *
     * @param  \App\Models\User  $user
     * @param  \App\Client  $client
     * @return mixed
     */
    public function show(User $user, Client $client)
    {
        return in_array($user->role->permissions, array('sudo', 'adviser', 'adviserprotection', 'admin'));
    }

    /**
     * Determine whether the user can view client
     *
     * @param  \App\Models\User  $user
     * @param  \App\Client  $client
     * @return mixed
     */
    public function edit(User $user, Client $client)
    {
        $user_id = Session::get('user_id',$user->id);
        if (
            $user->role->permissions == 'sudo' //is full admin
            || ($user->role->permissions == 'admin' && $client->account_id == $user->account_id) //is admin on account
            || $client->user_id == $user_id //is the adviser who the client belongs to
        ) {
            return true;
        }

        return false;
    }

}
