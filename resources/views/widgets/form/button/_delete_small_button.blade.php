@include('widgets.form.button._delete', 
        ['is_button'=>false, 
         'without_text' => 1,
         'route' => $obj_name.'.destroy', 
         'obj' => $$obj_name,
         'args'=>['id' => $$obj_name->id]])
