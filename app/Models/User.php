<?php

namespace App\Models;

use Cartalyst\Sentinel\Users\EloquentUser;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class User extends EloquentUser
{
   
    public function name()
    {
        return $this->first_name . ' '. $this->last_name;
    }
    
    
    /**
     * Gets a list of names of roles for the user.
     *
     * @param  int  $user_id
     * @return string
     */
    public static function getRolesNames(int $user_id) :String
    {
        //static::$rolesModel = $rolesModel;
        $roles = self::where('id',$user_id)->first()->roles()->get();
        $list = [];
        foreach ($roles as $role) {
            $list[] = $role->name;
        }
        return join(', ', $list);
    }
    
    /**
     * Checks access for a permission
     *
     * @param  string $permission, f.e. 'dict.edit'
     * @return boolean
     */
    public static function checkAccess(string $permission) : bool
    {
        $user=Sentinel::check();
        if (!$user)
            return false;
        if ($user->hasAccess($permission))
            return true;
        return false;
    }
    
    // "The permission display_name allows a user to description."
    
    // name,            display_name,       description
    // edit-user,       Edit users
    // config-system,   Configurate dictionary and corpus parameters
    // edit-dict,       Edit dictionary
    // edit-corpus,     Edit corpus
    // 
    
}
