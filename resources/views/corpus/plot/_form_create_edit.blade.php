        @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._select', 
                ['name' => 'genre_id', 
                 'values' =>$genre_values,
                 'title' => trans('corpus.genre')]) 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_ru', 
                 'title'=>trans('corpus.name').' '.trans('messages.in_russian')])                 
        @include('widgets.form.formitem._textarea', 
                ['name' => 'annot_ru', 
                 'title'=>trans('corpus.annot'),
                 'attributes' => ['rows'=>3],
                ])
    </div>
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'sequence_number', 
                 'title'=>trans('messages.sequence_number')])                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_en', 
                 'title'=>trans('corpus.name').' '.trans('messages.in_english')])                 
        @include('widgets.form.formitem._textarea', 
                ['name' => 'annot_en', 
                 'title'=>trans('corpus.annot'),
                 'attributes' => ['rows'=>3],
                ])
    </div>
</div>                 
@include('widgets.form.formitem._submit', ['title' => $submit_title])
