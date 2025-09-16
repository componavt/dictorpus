@extends('layouts.page')

@section('page_title')
{{ trans('navigation.synsets') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/lemma.css')!!}
@stop

@section('body')      
    @include('dict.synset._'.$action)
@stop

@section('footScriptExtra')
    {!! js('select2.min') !!}
    {!! js('list_change') !!}
    {!! js('synset') !!}
@stop

@section('jqueryFunc')
    selectSynsetMembers(".select-member", '{{ $url_args['search_lang'] }}', 'введите карельское или русское слово');
    
    $(".check-dominant").on('click', function() {
        id = $(this).data('id');
        $(".meaning-row").css('font-weight', 'normal');
        $("#meaning-"+id).css('font-weight', 'bold');
    });
@stop
