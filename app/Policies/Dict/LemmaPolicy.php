<?php

namespace App\Policies\Dict;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Dict\Lemma;

class LemmaPolicy
{
    use HandlesAuthorization;

    /**
     * Проверяет возможность создания леммы.
     *
     * @param User $user Проверяемый пользователь.
     * @param Lemma $lemma Лемма.
     * @return bool
     */
    public function store(User $user, Lemma $lemma)
    {
        return $this->canEdit($user);
    }

    /**
     * Проверяет возможность открытия редактора леммы.
     *
     * @param User $user Проверяемый пользователь.
     * @param Lemma $lemma Лемма.
     * @return bool
     */
    public function edit(User $user, Lemma $lemma)
    {
        return $this->canEdit($user);
    }

    /**
     * Проверяет возможность изменения леммы.
     *
     * @param User $user Проверяемый пользователь.
     * @param Lemma $lemma Лемма.
     * @return bool
     */
    public function update(User $user, Lemma $lemma)
    {
        return $this->canEdit($user);
    }

    /**
     * Учитывает общий переключатель и прежнее право пользователя.
     *
     * Проверяет именно переданного пользователя, а не текущую сессию.
     *
     * @param User $user Проверяемый пользователь.
     * @return bool
     */
    protected function canEdit(User $user)
    {
        return User::editingEnabled()
            && $user->hasAccess('dict.edit');
    }
}
