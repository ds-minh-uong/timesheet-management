<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

class UserPolicy
{
    use HandlesAuthorization;

    protected $role_admin = 1;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
     }

    public function manageUser(User $user)
    {
        return $user->role === User::ROLE_ADMIN;
    }

    public function view(User $user)
    {
        return $user->role === User::ROLE_ADMIN;
    }

    public function updateRole(User $user)
    {
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Timesheet $timesheet
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user)
    {
        return $user->role === User::ROLE_ADMIN;
    }

}
