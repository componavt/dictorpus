        @include('widgets.form.formitem._text', 
                ['name' => 'slug', 
                 'title'=> 'slug'])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name', 
                 'title'=> trans('auth.role_name')])
        
        <?php if ($action=='create') { $perm_value = []; } ?>        
        @foreach ($perm_values as $perm => $perm_t)   
            <?php $checked = (in_array($perm, $perm_value) ? 'checked' : NULL); ?>
            @include('widgets.form.formitem._checkbox', 
                    ['name' => 'permissions['.$perm.']', 
                     'value' => 'true',
                     'checked' => $checked,
                     'tail'=>$perm_t])
        @endforeach
{{--         @include('widgets.form.formitem._select', 
                ['name' => 'permissions[]', 
                 'values' =>$perm_values,
                 'value' => $perm_value,
                 'title' => trans('auth.permissions'),
                 'attributes'=>['multiple'=>'multiple']]) --}}

@include('widgets.form.formitem._submit', ['title' => $submit_title])
