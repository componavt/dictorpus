<?php

namespace App\Models\Auth;

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
    // "The permission display_name allows a user to description."
    
    // name,            display_name,       description
    // edit-user,       Edit users
    // config-system,   Configurate dictionary and corpus parameters
    // edit-dict,       Edit dictionary
    // edit-corpus,     Edit corpus
    // 
}
