        @include('widgets.form._formitem_text', 
                ['name' => 'email', 
                 'title'=> 'E-mail'])
                 
        @include('widgets.form._formitem_text', 
                ['name' => 'first_name', 
                 'title'=> trans('auth.first_name')])
                 
        @include('widgets.form._formitem_text', 
                ['name' => 'last_name', 
                 'title'=> trans('auth.last_name')])
                 
{{--       @include('widgets.form._formitem_checkbox', 
                ['name' => 'permissions[]', 
                 'value' => $permissions,
                 'title'=>trans('auth.permissions')]) --}}
        <?php if ($action=='create') { $perm_value = NULL; } ?>        
         @include('widgets.form._formitem_select', 
                ['name' => 'permissions[]', 
                 'values' =>$perm_values,
                 'value' => $perm_value,
                 'title' => trans('auth.permissions'),
                 'attributes'=>['multiple'=>'multiple']]) 

        <?php if ($action=='create') { $role_value = NULL; } ?>        
         @include('widgets.form._formitem_select', 
                ['name' => 'roles[]', 
                 'values' =>$role_values,
                 'value' => $role_value,
                 'title' => trans('auth.roles'),
                 'attributes'=>['multiple'=>'multiple']]) 
                 
@include('widgets.form._formitem_btn_submit', ['title' => $submit_title])
