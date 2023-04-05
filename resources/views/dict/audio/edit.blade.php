@extends('layouts.master')

@section('title')
{{ trans('navigation.audios') }}
@stop

@section('content')
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_audio')}}: <span class='imp'>{{ $audio->filename}}</span></h2>
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/audio/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>
        </p>
        
        {!! Form::model($audio, array('method'=>'PUT', 'route' => array('audio.update', $audio->id))) !!}
        @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
        <div class="row">
            <div class="col-sm-8">
        @include('widgets.form.formitem._select',
                ['name' => 'informant_id',
                 'values' =>$informant_values,
                 'title' => trans('corpus.informant')])                 
            </div>    
            <div class="col-sm-4">
        @include('widgets.form.formitem._text', 
                ['name' => 'filename', 
                 'title'=>trans('messages.filename')])
            </div>
        </div>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.save')])
                 
        {!! Form::close() !!}
@stop


