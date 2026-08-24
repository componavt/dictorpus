@include('widgets.form._url_args_by_post',['url_args'=>$url_args])
<input type="hidden" id="publication_field" value="">
@include('widgets.form.formitem._text', 
        ['name' => 'authors', 
         'title'=>trans('corpus.authors')])

@include('widgets.form.formitem._text', 
        ['name' => 'title', 
         'title'=>trans('corpus.title')])

@include('widgets.form.formitem._text', 
        ['name' => 'year', 
         'title'=>trans('messages.year')])

@include('widgets.form.formitem._text', 
        ['name' => 'addition_info', 
         'title'=>trans('corpus.addition_info')])
