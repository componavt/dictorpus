<?php

namespace App\Models;

use Cartalyst\Sentinel\Users\EloquentUser;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

use LaravelLocalization;

use App\Models\Role;

class User extends EloquentUser
{
    protected $fillable = ['email','first_name','last_name','permissions'];
    protected $perm_list = ['admin','edit.dict','edit.corpus'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }

    /** Gets name of this user
     * 
     * @return String
     */
    public function getNameAttribute()
    {
        return $this->first_name . ' '. $this->last_name;
    }
         
    // User __has_many__ Roles
    public function roles(){
        return $this->belongsToMany(Role::class, 'role_users');
    }
    
    /**
     * Get the fillable attributes for the model.
     *
     * @return array
     */
    public function getPermList()
    {
        $perms = $this->perm_list;
        $list = [];
        foreach ($perms as $p) {
            $list[$p] = \Lang::get("auth.perm.$p");
        }
        return $list;
    }

    /**
     * Gets a list of names of roles for the user.
     *
     * @return string
     */
    public function rolesNames()
    {
        $locale = LaravelLocalization::getCurrentLocale();

        $roles = $this->roles;
        $list = [];
        foreach ($roles as $role) {
            $list[] = $role->lname;
        }
        return join(', ', $list);
    }

    /**
     * Gets a list of permissions for the user.
     *
     * @return string
     */
    public function permissionString()
    {
        $permissions = $this->permissions;
        $list = [];
        
        foreach ($permissions as $key => $value) {
            $list[] = $key;
        }
        return join(', ', $list);
    }
    
    /**
     * Gets a list of names of roles for the user.
     *
     * @param  int  $user_id
     * @return string
     */
    public static function getRolesNames(int $user_id)
    {
        return self::where('id',$user_id)->first()->rolesNames();
    }
    
    /**
     * Checks access for a permission
     *
     * @param  string $permission, f.e. 'dict.edit'
     * @return boolean
     */
    public static function checkAccess(string $permission)
    {
        $user=Sentinel::check();
        if (!$user)
            return false;
//print "<pre>";
//var_dump($user);
        if ($user->hasAccess('admin') || $user->hasAccess($permission))
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
