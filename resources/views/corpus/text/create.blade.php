@extends('layouts.page')

@section('page_title')
{{ trans('navigation.texts') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
        <p>
            <a href="{{ route('text.index', $url_args) }}">{{ trans('messages.back_to_list') }}</a>
           |
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
            | <a href="{{ LaravelLocalization::localizeURL('/help/text/form') }}">? {{ trans('navigation.help') }}</a>
        </p>

        @include('corpus.text.modals_for_edition', ['action' => 'add'])
        
        {!! Form::open(['method'=>'POST', 'route' => ['text.store'], 'id'=>'textForm']) !!}
        @include('corpus.text.form._create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create',
                                      'readonly' => false,
                                      'text'=> null])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!! js('list_change') !!}
    {!!Html::script('js/corpus.js')!!}
    {!! js('publication')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    $(".multiple-select").select2();
    $(".multiple-select-bible").select2();
    
    selectDialect('textForm #lang_id');
    selectGenre('corpus_id');
    selectMotives('.multiple-select-motive', 'genres');
    selectPlot('.multiple-select-plot', 'genres');
    selectCycle('.multiple-select-cycle', 'genres');
    selectTopic('plots');
    
    selectPlot('.select-plot', 'genre_id'); /* from modal */
    initPublicationPeriodicFields();
    initSourcePubparts();
@stop