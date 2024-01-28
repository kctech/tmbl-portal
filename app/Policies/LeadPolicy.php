<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeadPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can do anything
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $current
     * @return mixed
     */
    public function anything(User $user)
    {
        return $user->role->level <= 1;
        //return $user->role->permissions == 'sudo';
    }

}
