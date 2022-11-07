        @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._select', 
                ['name' => 'genre_id', 
                 'values' =>$genre_values,
                 'value' => $motype->genre_id ?? $default_genre,
                 'title' => trans('corpus.genre')]) 
        @include('widgets.form.formitem._text', 
                ['name' => 'code', 
                 'title'=>trans('messages.code')])                 
    </div>
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'name_ru', 
                 'title'=>trans('corpus.name').' '.trans('messages.in_russian')])                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_en', 
                 'title'=>trans('corpus.name').' '.trans('messages.in_english')])                 
    </div>
</div>                 
@include('widgets.form.formitem._submit', ['title' => $submit_title])
