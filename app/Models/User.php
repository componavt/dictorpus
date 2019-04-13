<?php

namespace App\Models;

use Cartalyst\Sentinel\Users\EloquentUser;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

use LaravelLocalization;
use DB;

use App\Models\Role;
use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;

class User extends EloquentUser
{
    protected $fillable = ['email','first_name','last_name','permissions', 'country', 'city', 'affilation'];
    protected $perm_list = ['admin','dict.edit','corpus.edit','ref.edit','user.edit'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

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
    
    // User __has_many__ Langs
    public function langs(){
        return $this->belongsToMany(Lang::class, 'lang_user');
    }
    
    // User __has_many__ Langs
    public function dialects(){
        return $this->belongsToMany(Dialect::class, 'dialect_user');
    }
    
    public static function registration($input) {
        $sentuser = Sentinel::register($input);
        
        $user = self::find($sentuser->id);
        $user->city = $input['city'];
        $user->country = $input['country'];
        $user->affilation = $input['affilation'];
        $user->save();
        
        return $sentuser;
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
     * Gets a list of languages for the user.
     *
     * @return string
     */
    public function langString()
    {
        $langs = $this->langs;
        $list = [];
        
        foreach ($langs as $lang) {
            $list[] = $lang->name;
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
     * Gets IDs of langs for lang's form field
     *
     * @return Array
     */
    public function langValue():Array{
        $value = [];
        if ($this->langs) {
            foreach ($this->langs as $lang) {
                $value[] = $lang->id;
            }
        }
        return $value;
    }

    /**
     * Gets IDs of dialects for lang's form field
     *
     * @return Array
     */
    public function dialectValue():Array{
        $value = [];
        if ($this->dialects) {
            foreach ($this->dialects as $dialect) {
                $value[] = $dialect->id;
            }
        }
        return $value;
    }

    public function permValue():Array {        
        $user_perms = $this->permissions;

        $perm_value = [];
        foreach ($this->getPermList() as $perm=>$perm_t) {
            if (isset($user_perms[$perm]) && $user_perms[$perm]) {
                $perm_value[] = $perm;
            }
        }
        
        return $perm_value;
    }

    public function roleValue():Array {        
        $role_value = [];
        foreach ($this->roles as $role) {
            $role_value[] = $role->id;
        }
        return $role_value;
    }
    
    public static function authUser()
    {
        $auth_user=Sentinel::check();
        return self::find($auth_user->id);
    }
    
    /**
     * Gets the user first language ID
     * 
     * @return INT
     */
    public static function userLangID() {
        $user = self::authUser();
        if (!$user) {
            return NULL;
        }
        $langs = $user->langs;
        if (!$langs || !$langs->first()) {
            return NULL;
        }
        return  $langs->first()->id;
    } 
    
    public static function userDialectID() {
        $user = self::authUser();
        if (!$user) {
            return NULL;
        }
        $dialects = $user->dialects;
        if (!$dialects || !$dialects->first()) {
            return NULL;
        }
        return  $dialects->first()->id;
    } 
    
    public static function userDialects() {
        $user = self::authUser();
        if (!$user) {
            return NULL;
        }
        $dialects = $user->dialects;
        if (!$dialects) {
            return NULL;
        }
        $ids = [];
        
        foreach ($dialects as $dialect) {
            $ids[] = $dialect->id;
        }
        return  $ids;
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
    
    public function getLastActionTime() {
        $history = DB::table('revisions')
                     ->select('updated_at')
                     ->where('user_id',$this->id)
                     ->orderBy('updated_at','desc')->first();
        
        if ($history) {
            return $history->updated_at;
        }
    }
    
    public static function getNameByID($id) {
        $user = User::find($id);
        if ($user) {
            return $user->name;
        }
    }
    
     public static function countActiveEditors(){
        $now = date_create();
        $date = date_format(date_modify($now,'-30 day'), 'Y-m-d');
//dd(DB::table('revisions')->select('user_id')
//                 ->where('created_at','>',$date)->distinct()->toSQL());        
        $users = DB::table('revisions')->select('user_id')
                 ->where('created_at','>',$date)->groupBy('user_id')->get();
//dd($users);        
        return sizeof($users);
    }
   
    
    
    // "The permission display_name allows a user to description."
    
    // name,            display_name,       description
    // edit-user,       Edit users
    // config-system,   Configurate dictionary and corpus parameters
    // edit-dict,       Edit dictionary
    // edit-corpus,     Edit corpus
    // 
    
}
