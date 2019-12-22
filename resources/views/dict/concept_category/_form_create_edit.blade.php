        @include('widgets.form.formitem._text',
                ['name' => 'id',
                 'attributes'=>['size' => 4],
                 'title' => trans('messages.code')])         
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_en', 
                 'title'=>trans('dict.name').' '.trans('messages.in_english')])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_ru', 
                 'title'=>trans('dict.name').' '.trans('messages.in_russian')])
                                

@include('widgets.form.formitem._submit', ['title' => $submit_title])
