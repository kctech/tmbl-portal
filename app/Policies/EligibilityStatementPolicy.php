<?php

namespace App\Policies;

use App\User;
use App\EligibilityStatement;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Session;

class EligibilityStatementPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can do anything
     *
     * @param  \App\User  $user
     * @param  \App\Eligibility $eligibility
     * @return mixed
     */
    public function anything(User $user, Eligibility $eligibility)
    {
        //return $eligibility->user_id == $user->id;
        $user_id = Session::get('user_id',$user->id);
        //return $eligibility->user_id == $user_id; 
        if (
            $user->role->permissions == 'sudo' //is full admin
            || ($user->role->permissions == 'admin' && $eligibility->user->account_id == $user->account_id) //is admin on account
            || $eligibility->user_id == $user_id //is the adviser who the client belongs to
        ) { 
            return true;
        }

        return false;
    }
}
