<?php

namespace App\Policies;

use App\Models\User;
use App\Models\GdprConsent;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Session;

class GdprConsentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can do anything
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GdprConsent  $gdprConsent
     * @return mixed
     */
    public function anything(User $user, GdprConsent $gdprConsent)
    {
        //return $gdprConsent->user_id == $user->id;
        $user_id = Session::get('user_id',$user->id);
        //return $gdprConsent->user_id == $user_id;
        if (
            $user->role->permissions == 'sudo' //is full admin
            || ($user->role->permissions == 'admin' && $gdprConsent->user->account_id == $user->account_id) //is admin on account
            || $gdprConsent->user_id == $user_id //is the adviser who the client belongs to
        ) {
            return true;
        }

        return false;
    }

}
