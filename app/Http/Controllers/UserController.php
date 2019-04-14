<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use DB;
use LaravelLocalization;

use App\Models\User;
use App\Models\Role;
use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;

class UserController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin,/', ['all']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::getList();
        $users = [];
        
        foreach (Role::all() as $role) {
            $users[$role->id] = $role->users->sortByDesc('id');
        }
        
        return view('user.index',
                    compact('users','roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Redirect::to('/user/');
//        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return Redirect::to('/user/');
/*        $this->validate($request, [
            'first_name'  => 'required|max:255',
            'last_name'  => 'max:255',
            'email'  => 'required|email|max:150',
        ]);
        
        $user = User::create($request->all());
        
        return Redirect::to('/user/?search_id='.$user->id)
            ->withSuccess(\Lang::get('messages.created_success'));  
 * 
 */      
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Redirect::to('/user/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id); 
        
        $role_values = Role::getList();        
        $role_value = $user->roleValue();
        
        $perm_values = $user->getPermList();
        $perm_value = $user->permValue();

        $lang_values = Lang::getList();
        $lang_value = $user->langValue();
        
        $dialect_values = Dialect::getGroupedList();
        $dialect_value = $user->dialectValue();
        
        return view('user.edit',
                  compact('dialect_value', 'dialect_values', 'lang_value', 'lang_values', 
                          'perm_value', 'perm_values', 'role_value', 'role_values', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'first_name'  => 'required|max:255',
            'last_name'  => 'max:255',
            'email'  => 'required|email|max:150',
        ]);
        
        $user = User::find($id);
        $user->fill($request->only('email','first_name','last_name','country','city','affilation'));
        $user_perms = [];
        if ($request->permissions) {
            foreach ($request->permissions as $p) {
                $user_perms[$p] = true;
            }
        } 
//        $user->permissions = json_encode($user_perms);      
        $user->permissions = $user_perms;      
        $user->save();
        
        $user->roles()->detach();
        $user->roles()->attach($request->roles);
        
        $user->langs()->detach();
        $user->langs()->attach($request->langs);
        
        $user->dialects()->detach();
        $user->dialects()->attach($request->dialects);
        
        return Redirect::to('/user/?search_id='.$user->id)
            ->withSuccess(\Lang::get('messages.updated_success'));        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $error = false;
        $status_code = 200;
        $result =[];
        if($id != "" && $id > 0) {
            try{
                $user = User::find($id);
                if($user){
                    $user_name = $user->email;
                    $user->roles()->detach();
                    $user->delete();
                    $result['message'] = \Lang::get('auth.user_removed', ['name'=>$user_name]);
                }
                else{
                    $error = true;
                    $result['error_message'] = \Lang::get('messages.record_not_exists');
                }
          }catch(\Exception $ex){
                    $error = true;
                    $status_code = $ex->getCode();
                    $result['error_code'] = $ex->getCode();
                    $result['error_message'] = $ex->getMessage();
                }
        }else{
            $error =true;
            $status_code = 400;
            $result['message']='Request data is empty';
        }
        
        if ($error) {
                return Redirect::to('/user/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/user/')
                  ->withSuccess($result['message']);
        }
    }
}
