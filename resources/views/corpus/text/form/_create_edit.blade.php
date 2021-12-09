@include('widgets.form._url_args_by_post',['url_args'=>$url_args])

@include('corpus.text.form._corpus_dialects_genres')

<div class="row">
    <div class="col-sm-6">
@include('corpus.text.form._text')

@include('corpus.text.form._event')

        @include('widgets.form.formitem._text', 
                ['name' => 'youtube_id', 
                 'value' => ($action=='edit' && $text->video) ? ($text->video->youtube_id) : NULL,
                 'title'=>trans('corpus.youtube_id')])
                
@include('widgets.form.formitem._submit', ['title' => $submit_title])
    </div>
    
    <div class="col-sm-6">
@include('corpus.text.form._transtext')

@include('corpus.text.form._source')        

@include('corpus.text.form._archive_krc')        

        @include('widgets.form.formitem._textarea', 
                ['name' => 'source.comment', 
                 'value' => ($action=='edit' && $text->source) ? ($text->source->comment) : NULL,
                 'title'=>trans('corpus.comment'),
                 'attributes' => ['rows'=>3],
                ])
    </div>
</div>                 
                         


