<?php $list_count=1; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.audios') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/essential_audio.css')!!}
    {!!Html::style('css/essential_audio_circle.css')!!}
    {!!Html::style('css/essential_audio_circle_mini.css')!!}
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        @include('dict.audio._search_form',['url' => '/dict/audio']) 

        @include('widgets.found_records', ['numAll'=>$numAll])

        @if ($numAll)
        <table class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lemmas') }}</th>
                <th>{{ trans('dict.speaker') }}</th>
                <th>{{ trans('messages.updated_at') }}</th>
                <th>{{ trans('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($audios as $audio)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.lemmas') }}">
                    @foreach($audio->lemmas as $lemma)
                    <a href="{{ LaravelLocalization::localizeURL('dict/lemma/'.$lemma->id) }}">
                        {{$lemma->lemma}}
                    </a>
                    @endforeach
                </td>
                <td data-th="{{ trans('dict.speaker') }}">
                    {{$audio->informant ? $audio->informant->name : ''}}
                </td>
                <td data-th="{{ trans('messages.updated_at') }}">
                    {{$audio->updated_at}}
                </td>
                <td data-th="{{ trans('messages.actions') }}">
                    <div class='audio-button'>
                @include('widgets.audio_simple', ['route'=>$audio->url()])
                    </div>
                @if (User::checkAccess('dict.edit'))
                    @include('widgets.form.button._delete', 
                             ['is_button'=>false, 
                              'without_text' => true,
                              'route' => 'audio.destroy', 
                              'args'=>['id' => $audio->id]])
                @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
            {!! $audios->appends($url_args)->render() !!}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/essential_audio.js')!!}
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
    $(".multiple-select-lang").select2();
@stop

