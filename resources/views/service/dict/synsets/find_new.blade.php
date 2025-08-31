@extends('layouts.page')

@section('page_title')
{{ trans('navigation.synsets') }}
@stop

@section('headExtra')
    <link media="all" type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.11.4/css/dataTables.bootstrap.min.css">
    {!!Html::style('css/table.css')!!}
@stop

@section('body')  
    <h2>Найден новый синсет</h2>
    <p><b>язык:</b> {{ $lang->name }}</p>
    <p><b>часть речи:</b> {{ $pos->name }}</p>
    
        {!! Form::open(array('method'=>'POST', 'route' => array('synset.store'))) !!}
    <table>
        <tr><th colspan="2">{{ trans('dict.core') }}</th></tr>
    @foreach ($core as $meaning_id=>$member)
<?php //dd($member['meaning']);    ?>
        <tr>
            <td><a href="{{ route('lemma.show', $member['meaning']->lemma_id) }}">{{ $member['meaning']->lemma }}</a>: 
                {{ $member['meaning']->meaning_n}}. {{ $member['meaning']->getMeaningTextLocale() }} </td>
            <td>
                @include('widgets.form.formitem._select',
                        ['name' => 'syntype['.$member['meaning']->id.']',
                         'values' =>$syntype_values,
                         'title' => trans('dict.syntype')])
            </td>
        </tr>
    @endforeach
    
        <tr><th colspan="2">{{ trans('dict.periphery') }}</th></tr>
    @foreach ($periphery as $meaning_id=>$member)
        <tr>
            <td><a href="{{ route('lemma.show', $member['meaning']->lemma_id) }}">{{ $member['meaning']->lemma }}</a>: 
                {{ $member['meaning']->meaning_n}}. {{ $member['meaning']->getMeaningTextLocale() }} </td>
        </tr>
    @endforeach
    </table>
@include('widgets.form.formitem._submit', ['title' => trans('messages.create_new_m')])
        {!! Form::close() !!}
    
    <p><a href="{{ route('dict.synsets.index') }}">Вернуться к синсетам</a></p>
@stop

@section('footScriptExtra')
    <script src="//cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.11.4/js/dataTables.bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/sorting/numeric-comma.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/type-detection/numeric-comma.js"></script>
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/synset.js')!!}
@stop

@section('jqueryFunc')
@stop

