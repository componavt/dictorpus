@extends('layouts.page')

@section('page_title')
{{ trans('navigation.synsets') }}
@stop

@section('headExtra')
    <link media="all" type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.11.4/css/dataTables.bootstrap.min.css">
    {!!Html::style('css/table.css')!!}
@stop

@section('body')  
    <h2>Найдены новые синсеты</h2>
    <p><b>язык:</b> {{ $lang->name }}</p>
    
    @foreach ($synsets as $synset_n => $synset)
    <p class='warning'>Синсет No {{ $synset_n }}</p>
    <p><b>часть речи:</b> {{ $synset['pos_name'] }}</p>
    
    {!! Form::open(array('method'=>'POST', 'route' => array('synset.store'))) !!}
    <input type="hidden" name="lang_id" value="{{ $lang->id }}">
    
    <table style="width: 100%">
        @if (count($synset['core']))
        <tr><th colspan="2">{{ trans('dict.core') }}</th></tr>
            @foreach ($synset['core'] as $meaning_id=>$member)
<?php //dd($member['meaning']);    ?>
                @include('/dict/synset/_meaning_row', ['meaning'=>$member['meaning'], 'syntype_id'=>\App\Models\Dict\Syntype::TYPE_FULL])
            @endforeach
        @endif
        
        @if (count($synset['periphery']))
        <tr><th colspan="2">{{ trans('dict.periphery') }}</th></tr>
            @foreach ($synset['periphery'] as $meaning_id=>$member)
                @include('/dict/synset/_meaning_row', ['meaning'=>$member['meaning'], 'syntype_id'=>\App\Models\Dict\Syntype::TYPE_PART])
            @endforeach
        @endif
    </table>

    @include('widgets.form.formitem._textarea',
            ['name' => 'comment',
             'attributes' => ['rows' => 3],
             'title' => trans('corpus.comment')])
        
    @include('widgets.form.formitem._submit', ['title' => trans('messages.create_new_m')])
    {!! Form::close() !!}
    
    <hr>
    @endforeach
    
    <p><a href="{{ route('dict.synsets.index', $url_args) }}">Вернуться к синсетам</a></p>
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

