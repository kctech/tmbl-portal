<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ClientTransferConsent;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Session;

class ClientTransferConsentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can do anything
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClientTransferConsent  $clientTransferConsent
     * @return mixed
     */
    public function anything(User $user, ClientTransferConsent $clientTransferConsent)
    {
        //return $clientTransferConsent->user_id == $user->id;
        $user_id = Session::get('user_id',$user->id);
        //return $clientTransferConsent->user_id == $user_id;
        if (
            $user->role->permissions == 'sudo' //is full admin
            || ($user->role->permissions == 'admin' && $clientTransferConsent->user->account_id == $user->account_id) //is admin on account
            || $clientTransferConsent->user_id == $user_id //is the adviser who the client belongs to
        ) {
            return true;
        }

        return false;
    }

}
