        @include('widgets.form.formitem._text', 
                ['name' => 'name_en', 
                 'title'=>trans('messages.name').' '.trans('messages.in_english')])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_ru', 
                 'title'=>trans('messages.name').' '.trans('messages.in_russian')])
