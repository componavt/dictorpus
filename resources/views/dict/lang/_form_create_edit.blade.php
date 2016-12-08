        @include('widgets.form._formitem_text', 
                ['name' => 'name_en', 
                 'title'=>trans('dict.name').' '.trans('messages.in_english')])
                 
        @include('widgets.form._formitem_text', 
                ['name' => 'name_ru', 
                 'title'=>trans('dict.name').' '.trans('messages.in_russian')])
                 
        @include('widgets.form._formitem_text',
                ['name' => 'code',
                 'title' => trans('messages.code')])         

@include('widgets.form._formitem_btn_submit', ['title' => $submit_title])
