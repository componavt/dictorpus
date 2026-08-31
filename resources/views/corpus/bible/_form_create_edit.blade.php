@include('widgets.form._url_args_by_post',['url_args'=>$url_args])

@include('widgets.form.formitem._text', 
        ['name' => 'name_ru', 
            'title'=>trans('corpus.name').' '.trans('messages.in_russian')])                 
@include('widgets.form.formitem._text', 
        ['name' => 'name_en', 
            'title'=>trans('corpus.name').' '.trans('messages.in_english')])                 
@include('widgets.form.formitem._text', 
        ['name' => 'sequence_number', 
            'title'=>trans('messages.sequence_number')])                 

@include('widgets.form.formitem._submit', ['title' => $submit_title])
