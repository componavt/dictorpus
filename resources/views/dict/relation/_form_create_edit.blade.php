        @include('widgets.form.formitem._text', 
                ['name' => 'name_en', 
                 'title'=>trans('dict.name').' '.trans('messages.in_english')])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_ru', 
                 'title'=>trans('dict.name').' '.trans('messages.in_russian')])
                 
        @include('widgets.form.formitem._select', 
                ['name' => 'reverse_relation_id', 
                 'values' =>$relation_values,
                 'title' => trans('dict.reverse_relation')]) 
                 
        @include('widgets.form.formitem._text',
                ['name' => 'sequence_number',
                 'attributes'=>['size' => 2],
                 'title' => trans('messages.sequence_number')])         

@include('widgets.form.formitem._submit', ['title' => $submit_title])
