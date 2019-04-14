        @include('widgets.form.formitem._text', 
                ['name' => 'email', 
                 'title'=> 'E-mail'])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'first_name', 
                 'title'=> trans('auth.first_name')])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'last_name', 
                 'title'=> trans('auth.last_name')])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'country', 
                 'title'=> trans('auth.country')])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'city', 
                 'title'=> trans('auth.city')])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'affilation', 
                 'title'=> trans('auth.affilation')])
                 
        <?php if ($action=='create') { $perm_value = NULL; } ?>        
         @include('widgets.form.formitem._select2', 
                ['name' => 'permissions', 
                 'values' =>$perm_values,
                 'value' => $perm_value,
                 'title' => trans('auth.permissions'),
                 'class'=>'multiple-select form-control'
            ])

        <?php if ($action=='create') { $role_value = NULL; } ?>        
         @include('widgets.form.formitem._select2', 
                ['name' => 'roles', 
                 'values' =>$role_values,
                 'value' => $role_value,
                 'title' => trans('auth.roles'),
                 'class'=>'multiple-select form-control'
            ])
                 
        @include('widgets.form.formitem._select2',
                ['name' => 'langs', 
                 'values' =>$lang_values,
                 'value' => $lang_value,
                 'title' => trans('navigation.langs'),
                 'class'=>'multiple-select form-control'
            ])
            
        @include('widgets.form.formitem._select2',
                ['name' => 'dialects', 
                 'values' =>$dialect_values,
                 'value' => $dialect_value,
                 'title' => trans('navigation.dialects'),
                 'grouped' => true,
                 'class'=>'multiple-select form-control'
            ])
@include('widgets.form.formitem._submit', ['title' => $submit_title])
