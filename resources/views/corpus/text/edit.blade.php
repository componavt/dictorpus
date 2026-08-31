@extends('layouts.page')

@section('page_title')
{{ trans('navigation.texts') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_text')}} <span class='imp'>"{{ $text->title}}"</span></h2>
        <p>
            <a href="{{ route('text.index', $url_args) }}">{{ trans('messages.back_to_list') }}</a> |
            <a href="{{ route('text.show', $text->id) }}{{ $args_by_get}}">{{ trans('messages.back_to_show') }}</a> |            
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
            | <a href="{{ LaravelLocalization::localizeURL('/help/text/form') }}">? {{ trans('navigation.help') }}</a>
        </p>

        @include('corpus.text.modals_for_edition', ['action' => 'edit'])
        
        {!! Form::model($text, ['method'=>'PUT', 'route'=>['text.update', $text->id], 'files'=>true, 'id'=>'textForm']) !!} <?php //, 'enctype'=>"multipart/form-data"?>
        @include('corpus.text.form._create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit',
                                      'motive_value'=> $text->motiveValue()])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/corpus.js')!!}
    {!! js('publication')!!}
@stop

@section('jqueryFunc')
{{--    uploadAudio({{$text->id}}, '{{route('audiotext.upload')}}'); --}}
    toggleSpecial();
    $(".multiple-select").select2();
    $(".multiple-select-bible").select2();
    
    selectDialect('lang_id');
    selectGenre('corpus_id');
    selectPlot('.multiple-select-plot', 'genres');
    selectCycle('.multiple-select-cycle', 'genres');
    selectMotives('.multiple-select-motive', 'genres');
    selectTopic('plots');
    selectPubpart('publication_id');
    
    selectPlot('.select-plot', 'genre_id'); /* from modal */
    initPublicationPeriodicFields(); 
    initSourcePubparts();   
    
    $('.text-unlock').click(function() {
        $(this).hide();
        $('#text').prop('readonly',false);
        $('#to_makeup').prop('disabled',false);
        $('#text_structure').prop('readonly',false);
        $('#to_makeup_label').css('text-decoration','none')
                             .css('color','#972d1a')
                             .css('font-weight','bold');
    });
@stop
