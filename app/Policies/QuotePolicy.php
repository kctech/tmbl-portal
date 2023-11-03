<?php

namespace App\Policies;

use App\Models\User;
use App\Quote;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Session;

class QuotePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can do anything
     *
     * @param  \App\Models\User  $user
     * @param  \App\TermsConsentPolicy  $quotePolicy
     * @return mixed
     */
    public function anything(User $user, Quote $quotePolicy)
    {
        //return $quotePolicy->user_id == $user->id;
        $user_id = Session::get('user_id',$user->id);
        //return $quotePolicy->user_id == $user_id;
        if (
            $user->role->permissions == 'sudo' //is full admin
            || ($user->role->permissions == 'admin' && $quotePolicy->user->account_id == $user->account_id) //is admin on account
            || $quotePolicy->user_id == $user_id //is the adviser who the client belongs to
        ) {
            return true;
        }

        return false;
    }

}
