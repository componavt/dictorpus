@extends('layouts.master')

@section('title')
{{ trans('navigation.audios') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('content')
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_audio')}}: <span class='imp'>{{ $audio->filename}}</span></h2>
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/audio/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>
        </p>
        
        {!! Form::model($audio, array('method'=>'PUT', 'route' => array('audio.update', $audio->id))) !!}
        @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
        <div class="row">
            <div class="col-sm-4">
        @include('widgets.form.formitem._text', 
                ['name' => 'filename', 
                 'title'=>trans('messages.filename')])
            </div>
            <div class="col-sm-8">
        @include('widgets.form.formitem._select',
                ['name' => 'informant_id',
                 'values' =>$informant_values,
                 'title' => trans('corpus.informant')])                 
            </div>    
        </div>
        <div class="row">
            <div class="col-sm-4">
        @include('widgets.form.formitem._select',
                ['name' => 'lang_id',
                 'values' =>$lang_values,
                 'value' =>$lang_id,
                 'title' => trans('dict.lang'),
                 'attributes' => ['id'=>'lemma_lang_id']])
            </div>
            <div class="col-sm-8">                                  
        @include('widgets.form.formitem._select2',
                ['name' => 'lemmas',
                 'title' => trans('dict.lemmas'),
                 'values' => $lemma_values,
                 'value' => array_keys($lemma_values),
                 'class'=> 'multiple-select-lemmas'
            ])
            </div>    
        </div>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.save')])
                 
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/list_change.js')!!}
@stop

@section('jqueryFunc')
    selectWithLang('.multiple-select-lemmas', "/dict/lemma/list_with_pos_meaning", 'lang_id');
    changeLangOfInformant('#informant_id');
/*    $(".multiple-select-lemmas").select2({
        width: '100%',
        ajax: {
          url: "",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              lang_id: $( "#lang_id option:selected" ).val(),
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    });*/
@stop


