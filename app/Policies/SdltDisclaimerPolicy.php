<?php

namespace App\Policies;

use App\Models\User;
use App\SdltDisclaimer;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Session;

class SdltDisclaimerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can do anything
     *
     * @param  \App\Models\User  $user
     * @param  \App\SdltDisclaimer  $sdltDisclaimer
     * @return mixed
     */
    public function anything(User $user, SdltDisclaimer $sdltDisclaimer)
    {
        //return $sdltDisclaimer->user_id == $user->id;
        $user_id = Session::get('user_id',$user->id);
        //return $sdltDisclaimer->user_id == $user_id;
        if (
            $user->role->permissions == 'sudo' //is full admin
            || ($user->role->permissions == 'admin' && $sdltDisclaimer->user->account_id == $user->account_id) //is admin on account
            || $sdltDisclaimer->user_id == $user_id //is the adviser who the client belongs to
        ) {
            return true;
        }

        return false;
    }

}
