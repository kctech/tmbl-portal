<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AccountModule;
use Illuminate\Auth\Access\HandlesAuthorization;

use Illuminate\Support\Facades\Session;

class AccountModulePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can do anything
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BtlConsent  $btlConsent
     * @return mixed
     */
    public function module($accountModule)
    {
        //public function module(User $user, $accountModule)
        //$modules = json_decode(getModules($user));
        $modules = json_decode(Session::get('modules'));
        if (in_array($accountModule, $modules)) {
            return true;
        } else {
            return false;
        }
    }

}
