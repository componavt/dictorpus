        @include('widgets.form.formitem._select_for_field', 
                ['name' => 'pos_category', 
                 'values' => trans('dict.pos_categories'),
                 'lang_file'=> 'dict'])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_en', 
                 'title'=>trans('dict.name').' '.trans('messages.in_english')])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_ru', 
                 'title'=>trans('dict.name').' '.trans('messages.in_russian')])
                 
        @include('widgets.form.formitem._text',
                ['name' => 'sequence_number',
                 'attributes'=>['size' => 2],
                 'title' => trans('messages.sequence_number')])         

@include('widgets.form.formitem._submit', ['title' => $submit_title])
