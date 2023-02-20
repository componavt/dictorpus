@extends('layouts.page')

@section('page_title')
{{ trans('dict.dict_sound') }}
@stop

@section('headExtra')
    <link media="all" type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.11.4/css/dataTables.bootstrap.min.css">
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/mic.css')!!}
@stop

@section('body')        
    @include('corpus.informant._about')
    <p><a href="{{ LaravelLocalization::localizeURL('corpus/informant/'.$informant->id).'/audio'}}">Вернуться к информанту</a>
    
    <h3>{{trans('dict.add-lemmas-for-voicing')}}</h3>
    <p>Слов в <a href="{{ LaravelLocalization::localizeURL('dict/audio/list/'.$informant->id) }}">списке для озвучивания</a>: {{$informant->lemmas()->count()}}</p>
    
    @include('dict.audio.list._choose_search')
    
    @include('dict.audio.list._choose_list')        
    
@stop

@section('footScriptExtra')
    <script src="//cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.11.4/js/dataTables.bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/sorting/numeric-comma.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/type-detection/numeric-comma.js"></script>
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/mic.js')!!}
@stop

@section('jqueryFunc')    
{{--    $('#lemmasTable').DataTable( {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.4/i18n/ru.json'
        },
        "order": [[ 2, "asc" ]]
    } ); --}}
    
    selectAllFields('select-all-lemmas', '.choose-lemma');
@stop

