@include('widgets.form.button._delete', 
        ['is_button'=>true, 
         'without_text' => 1,
         'route' => $obj_name.'.destroy', 
         'obj' => $$obj_name])
