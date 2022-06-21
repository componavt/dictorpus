@include('dict.example._create_edit_form', 
            ['example_id'=>$example->id, 
             'example'=>$example->example, 
             'example_ru'=>$example->example_ru, 
             'func'=>'updateExample('.$example->id.')'] )
