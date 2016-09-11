        @include('widgets.form._formitem_text', 
                ['name' => 'name_en', 
                 'title'=>trans('corpus.name').' '.trans('messages.in_english')])
                 
        @include('widgets.form._formitem_text', 
                ['name' => 'name_ru', 
                 'title'=>trans('corpus.name').' '.trans('messages.in_russian')])
                 
@include('widgets.form._formitem_btn_submit', ['title' => $submit_title])
