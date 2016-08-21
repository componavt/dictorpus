<?php

namespace App\Models;

use Cartalyst\Sentinel\Users\EloquentUser;

class User extends EloquentUser
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
 /*   protected $fillable = [
        'last_name', 'first_name', 'email', 'password',
    ];*/

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
 /*   protected $hidden = [
        'password', 'remember_token',
    ];*/
    
    public function name()
    {
        return $this->first_name . ' '. $this->last_name;
    }
}
