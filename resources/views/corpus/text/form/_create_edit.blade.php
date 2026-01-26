@include('widgets.form._url_args_by_post',['url_args'=>$url_args])

@include('corpus.text.form._corpus_dialects_genres')

<div class="row">
    <div class="col-sm-6">
@include('corpus.text.form._text')

@include('corpus.text.form._cyrtext')

@include('corpus.text.form._event')

@include('widgets.form.formitem._select2', 
        ['name' => 'places', 
         'values' =>$place_values,
         'value' => !empty($text) ? $text->placeValue() : [],
         'call_add_onClick' => "addPlace('place_id')",
         'call_add_title' => trans('messages.create_new_g'),
         'title' => trans('corpus.place_mentioned')]) 
                 
@include('widgets.form.formitem._select2', 
        ['name' => 'celebration_types', 
         'values' =>trans('corpus.celebration_types'),
         'value' => !empty($text) ? $text->getCelebrationTypeIds() : [],
         'title' => trans('corpus.celebration_type')]) 
                 
<br>
@include('widgets.form.formitem._submit', ['title' => $submit_title])
    </div>
    
    <div class="col-sm-6">
@include('corpus.text.form._transtext')

@include('corpus.text.form._source')        

@include('corpus.text.form._archive_krc')        

@include('widgets.form.formitem._textarea', 
        ['name' => 'source.comment', 
         'value' => ($action=='edit' && $text->source) ? ($text->source->comment) : NULL,
         'title'=>trans('corpus.comment_source'),
         'attributes' => ['rows'=>3],
        ])
@include('widgets.form.formitem._textarea', 
        ['name' => 'comment', 
         'value' => ($action=='edit') ? ($text->comment) : NULL,
         'title'=>trans('corpus.comment_text'),
         'attributes' => ['rows'=>3],
        ])
@include('widgets.form.formitem._text', 
        ['name' => 'youtube_id', 
         'value' => ($action=='edit' && $text->video) ? ($text->video->youtube_id) : NULL,
         'title'=>trans('corpus.youtube_id')])

@if ($action=='edit')         
<input type='hidden' id='text_id' value='{{$text->id}}'>
        <p> <b>Аудиофайлы</b>         
            <i onclick="addAudio({{$text->id}})" class="call-add fa fa-plus fa-lg" title="Добавить новый"></i>
        </p>
        <div id='choosed_files'>
            @include('corpus.audiotext._show_files',['audiotexts'=>$text->audiotexts])
        </div>
        <div class="audiotext-upload">
            <input type="text" name="new_file_name" value="{{$text->newAudiotextName()}}">
            <input type="file" name="new_file">
        </div>
<!--input id="file" type="file">
<br>
<div class="progress">
    <div class="progress-bar" 
         role="progressbar" aria-valuemin="0"
         aria-valuemax="100">

    </div>
</div-->
@endif
    </div>
</div>                 

@include('corpus.text.form._folk_fields')



