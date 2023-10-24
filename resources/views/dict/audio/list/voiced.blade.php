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
    <p><a href="{{ LaravelLocalization::localizeURL('dict/audio/list/'.$informant->id. '/record') }}">{{trans('dict.voice_new_lemmas')}}</a> ({{$informant->lemmas()->count()}})</p>
    <p><a href="{{ LaravelLocalization::localizeURL('dict/audio/list/'.$informant->id) }}">{{trans('dict.edit_list_for_voicing')}}</a> ({{$informant->lemmas()->count()}})</p>
    <p><a href="{{ LaravelLocalization::localizeURL('dict/audio/list/'.$informant->id) .'/choose'}}">{{trans('dict.add-lemmas-for-voicing')}}</a> 
        ({{ format_number($informant->unvoicedLemmasCount()) }} / {{ format_number($informant->unvoicedDialectLemmasCount()) }})</p>

    <h3>{{trans('dict.voiced_lemmas')}}</h3>
    @include('dict.audio.list._voiced_list')
    
@stop

@section('footScriptExtra')
    <script src="//cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.11.4/js/dataTables.bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/sorting/numeric-comma.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/type-detection/numeric-comma.js"></script>
    {!!Html::script('js/mic.js')!!}
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
    
    recordAudio('{{$informant->id}}', '{{ csrf_token() }}');
    
    $('#audiosTable').DataTable( {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.4/i18n/ru.json'
        },
        "order": [[ 0, "asc" ]]
    } );    
    
@stop


