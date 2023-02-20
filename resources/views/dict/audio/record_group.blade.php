@extends('layouts.page')

@section('page_title')
Запись аудио {{$list_title}}
@stop

@section('headExtra')
    {!!Html::style('css/mic.css')!!}
@stop

@section('body')
<div class="row">
    <div class="col-sm-2" style="text-align: right">{{trans('corpus.record_by')}}</div>
    <div class="col-sm-10">
        @include('widgets.form.formitem._select', 
                ['name' => 'informant_id', 
                 'values' =>$informant_values,
                 'value' => $informant_id]) 
    </div>
</div>
    @include('dict.audio.list._record_block')
@stop

@section('jqueryFunc')
    @include('dict.audio._record_js')
@stop
