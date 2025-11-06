@extends('layouts.page')

@section('page_title', @trans('navigation.monuments'))

@section('headExtra')
    <link href="/css/select2.min.css" rel="stylesheet">
    {!! css('bootstrap-datepicker3.min') !!}
@endsection
                      
@section('body')
@include('corpus.monument._'.$action)
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!! js('bootstrap-datepicker.min') !!}
    {!! js('bootstrap-datepicker.ru.min') !!}
@stop

@section('jqueryFunc')
    selectDialect('lang_id', '', true);
    
    $('#publ_date_from').datepicker({
        format: 'mm.yyyy',       // формат отображения: 06.2025
        startView: 2,            // начинать с выбора года (0 = дни, 1 = месяцы, 2 = годы)
        minViewMode: 1,          // минимальный режим — выбор месяца
        autoclose: true,         // закрывать после выбора
        language: 'ru',          // русская локализация
        todayHighlight: true
    });
    
    $('#publ_date_to').datepicker({
        format: 'mm.yyyy',  
        startView: 2,       
        minViewMode: 1,        
        autoclose: true,      
        language: 'ru',       
        todayHighlight: true
    });
    
@stop