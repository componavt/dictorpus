<?php //$list_count=1; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.audios') }}
@stop

@section('headExtra')
    <link media="all" type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.11.4/css/dataTables.bootstrap.min.css">
    {!!Html::style('css/essential_audio.css')!!}
    {!!Html::style('css/essential_audio_circle.css')!!}
    {!!Html::style('css/essential_audio_circle_mini.css')!!}
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/mic.css')!!}
@stop

@section('body')        
    <h2>{{trans('dict.record_by_informant')}} {{$informant->name}}</h2>
    
    @if ($informant->birth_place)
    <p><b>{{trans('corpus.birth_place')}}</b>: {{$informant->birth_place->placeString('', false)}}</p>
    @endif
    
    @if ($informant->lang)
    <p><b>{{trans('dict.lang')}}</b>: {{$informant->lang->name}}</p>
    @endif

    @include('dict.audio.list._create_list')
    @include('dict.audio.list._show_list')
    
@stop

@section('footScriptExtra')
    <script src="//cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.11.4/js/dataTables.bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/sorting/numeric-comma.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/type-detection/numeric-comma.js"></script>
    {!!Html::script('js/essential_audio.js')!!}
    {!!Html::script('js/mic.js')!!}
@stop

@section('jqueryFunc')
    recordAudio('{{$informant->id}}', '{{ csrf_token() }}');
    
    $('#audiosTable').DataTable( {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.4/i18n/ru.json'
        },
        "order": [[ 0, "asc" ]]
    } );    
@stop

