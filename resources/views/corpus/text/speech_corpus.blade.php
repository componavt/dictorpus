<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.speech_corpus') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/text.css')!!}
    {!!Html::style('css/table.css')!!}
    {!!Html::style('css/buttons.css')!!}
@stop

@section('body')
        <a href="{{ LaravelLocalization::localizeURL('/corpus/audiotext/map') }}">{{ trans('navigation.audio_map') }}</a> |
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
            | <a href="{{ LaravelLocalization::localizeURL('/help/text/search') }}">? {{ trans('navigation.help') }}</a>
        </p>
        
        @include('widgets.modal',['name'=>'modalHelp',
                                  'title'=>trans('navigation.help'),
                                  'modal_view'=>'help.text._search'])
                                  
        @include('corpus.text.form._search', ['form_url'=> '/corpus/speech_corpus/', 'full'=>false]) 

        @include('widgets.found_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
        <table class="table-bordered table-striped table-wide rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
{{--            @if (!$url_args['search_lang'])
                <th>{{ trans('dict.lang') }}</th>
            @endif --}}
            @if (!$url_args['search_dialect'])
                <th>{{ trans('dict.dialect') }}</th>
            @endif
                <th>{{ trans('corpus.speech') }}</th>
                <th>{{ trans('corpus.title') }}</th>
                <th>{{ trans('messages.translation') }}</th>
                <th>{{ trans('corpus.listen') }}</th>
                
                @if (User::checkAccess('corpus.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($texts as $text)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
{{--            @if (!$url_args['search_lang'])
                <td data-th="{{ trans('dict.lang') }}">{{$text->lang->name}}</td>
            @endif --}}
            @if (!$url_args['search_dialect'])
                <td data-th="{{ trans('dict.dialect') }}">
                    @if($text->dialects)
                        @foreach ($text->dialects as $dialect)
                        {{$dialect->name}}<br>
                        @endforeach
                        
                    @endif
                </td>
            @endif

                <td data-th="{{ trans('corpus.speech') }}">
                    @if ($text->speech)
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/place?search_id='.$text->speech->id) }}">{{ $text->speech->name }}</a>
                    @endif
                </td>
            
                <td data-th="{{ trans('corpus.title') }}">
                    {{ $text->authorsToString() ? $text->authorsToString().'.' : '' }}
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}{{$args_by_get}}">{{$text->title}}</a>
                @if ($url_args['search_word'] && $text->transtext)
                    <br>({{$text->transtext->title}})
                @endif
                </td>
                <td data-th="{{ trans('messages.translation') }}">
                    @if ($text->transtext)
                    {{ $text->transtext->authorsToString() ? $text->transtext->authorsToString().'.' : '' }}
                    {{$text->transtext->title}}
                    @endif
                </td>
                <td data-th="{{ trans('corpus.listen') }}">
                    @include('corpus.audiotext._show_files',['audiotexts'=>$text->audiotexts])
                </td>
                
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/text/'.$text->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'text.destroy', 
                             'args'=>['id' => $text->id]])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $texts->appends($url_args)->render() !!}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/search.js')!!}
    {!!Html::script('js/help.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    toggleSearchForm();
    recDelete('{{ trans('messages.confirm_delete') }}');
    $(".multiple-select-lang").select2();
    $(".multiple-select-corpus").select2();
    selectGenre();
    selectWithLang('.multiple-select-dialect', "/dict/dialect/list", 'search_lang', '', true);
    selectPlot('.multiple-select-plot', 'search_genre');
    selectTopic('search_plot');
    selectGenre();
    selectDistrict();
    selectPlace();
    selectBirthDistrict();
    selectBirthPlace();
@stop
