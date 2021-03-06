        @include('widgets.form.formitem._text', 
                ['name' => 'name_en', 
                 'title'=>trans('dict.name').' '.trans('messages.in_english')])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_ru', 
                 'title'=>trans('dict.name').' '.trans('messages.in_russian')])
                 
        @include('widgets.form.formitem._text',
                ['name' => 'code',
                 'title' => trans('messages.code')])         

        @include('widgets.form.formitem._text',
                ['name' => 'sequence_number',
                 'title' => trans('messages.sequence_number')])         

@include('widgets.form.formitem._submit', ['title' => $submit_title])
