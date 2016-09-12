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

class RoleController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin,/role/', ['all']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('slug')->get();
        
        return view('role.index')
                    ->with(['roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $perm_values = (new User)->getPermList();
        return view('role.create')->with(['perm_values' => $perm_values]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = Role::create($request->all());

        return Redirect::to('/role/')
            ->withSuccess(\Lang::get('messages.created_success'));  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Redirect::to('/role/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id); 
        
        $perm_values = (new User)->getPermList();
        $role_perms = $role->permissions;

        $perm_value = [];
        foreach ($perm_values as $perm=>$perm_t) {
            if (isset($role_perms[$perm]) && $role_perms[$perm]) {
                $perm_value[] = $perm;
            }
        }
       
        return view('role.edit')
                  ->with(['role' => $role,
                          'perm_values' => $perm_values,
                          'perm_value' => $perm_value,
                         ]);
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
        $role = Role::find($id);
        $role->fill($request->all());
        if (!isset($request->permissions)) {
            $role->permissions = [];
        }
        $role->save();
        
        return Redirect::to('/role/?search_id='.$role->id)
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
                $role = Role::find($id);
                if($role){
                    $role_name = $role->name;
                    $role->users()->detach();
                    $role->delete();
                    $result['message'] = \Lang::get('role_removed', ['name'=>$role_name]);
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
                return Redirect::to('/role/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/role/')
                  ->withSuccess($result['message']);
        }
    }
}
