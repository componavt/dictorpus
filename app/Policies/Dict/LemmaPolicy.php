<?php

namespace App\Policies\Dict;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Dict\Lemma;

class LemmaPolicy
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
     * Determine if the given lemma can be updated by the user.
     *
     * @param  User  $user
     * @param  Lemma  $lemma
     * @return bool
     */

    public function store(User $user, Lemma $lemma)
    {
        $permission = 'dict.edit';
        return $user->hasAccess($permission);
    }
    
    public function update(User $user, Lemma $lemma)
    {
        $permission = 'dict.edit';
        return $user->hasAccess($permission);
    }
}
