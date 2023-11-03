<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BtlConsent;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Session;

class BtlConsentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can do anything
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BtlConsent  $btlConsent
     * @return mixed
     */
    public function anything(User $user, BtlConsent $btlConsent)
    {
        //return $btlConsent->user_id == $user->id;
        $user_id = Session::get('user_id',$user->id);
        //return $btlConsent->user_id == $user_id;
        if (
            $user->role->permissions == 'sudo' //is full admin
            || ($user->role->permissions == 'admin' && $btlConsent->user->account_id == $user->account_id) //is admin on account
            || $btlConsent->user_id == $user_id //is the adviser who the client belongs to
        ) {
            return true;
        }

        return false;
    }

}
