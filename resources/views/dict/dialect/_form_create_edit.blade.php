        @include('widgets.form._url_args_by_post',['url_args'=>$url_args])

        @include('widgets.form._formitem_select',
                ['name' => 'lang_id',
                 'values' =>$lang_values,
                 'title' => trans('dict.lang'),
                 'attributes' => ['id'=>'lemma_lang_id']])
                 
        @include('widgets.form._formitem_text', 
                ['name' => 'name_en', 
                 'title'=>trans('dict.name').' '.trans('messages.in_english')])
                 
        @include('widgets.form._formitem_text', 
                ['name' => 'name_ru', 
                 'title'=>trans('dict.name').' '.trans('messages.in_russian')])
                 
        @include('widgets.form._formitem_text',
                ['name' => 'code',
                 'title' => trans('dict.code')])         

@include('widgets.form._formitem_btn_submit', ['title' => $submit_title])
