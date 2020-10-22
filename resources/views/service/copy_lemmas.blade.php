<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.copy_lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
{!! Form::open(['url' => '/service/copy_lemmas', 'method' => 'get']) !!}
<div class="row">
    <div class="col-sm-4">
        @include('widgets.form.formitem._select',
                ['name' => 'lang_from',
                 'values' =>$lang_values,
                 'value' =>$url_args['lang_from'],
                 'title' => trans('dict.lang_from'),
                 'attributes'=>['id'=>'lemma_lang_id']])
    </div>        
    <div class="col-sm-4">
        @include('widgets.form.formitem._select',
                ['name' => 'lang_to',
                 'values' =>$lang_values,
                 'value' =>$url_args['lang_to'],
                 'title' => trans('dict.lang_to') ])
    </div>        
    <div class="col-sm-4">
         @include('widgets.form.formitem._select2',
                ['name' => 'wordform_dialect_id', 
                 'values' =>$dialect_values,
                 'value' => $url_args['wordform_dialect_id'] ?? null,
                 'is_multiple' => false,
                 'title' => trans('dict.dialect_in_lemma_form'),
                 'class'=>'select-wordform-dialect form-control'])
   </div>
</div>
<div class="row">
    <div class="col-sm-4">
        @include('widgets.form.formitem._text',
                ['name' => 'search_lemma',
                'value' => $url_args['search_lemma'],
                'special_symbol' => true,
                'attributes'=>['placeholder'=>trans('dict.lemma')]])                               
    </div>   
    <div class="col-sm-4">
            @include('widgets.form.formitem._select',
                    ['name' => 'search_pos',
                     'values' =>$pos_values,
                     'value' =>$url_args['search_pos'],
                     'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 
    </div>
    
    <div class="col-sm-4 search-button-b">       
        <span>{{trans('messages.show_by')}}</span>
        @include('widgets.form.formitem._text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>[ 'placeholder' => trans('messages.limit_num') ]]) 
        <span>{{ trans('messages.records') }}</span>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>     
        @if (!$dialect_is_right) {
            <p class='warning'>{{ trans('dict.dialect_wrong') }}!</p>
        @elseif($added_lemmas)
            <p class='warning'>{{ trans('dict.lemmas_added', ['count'=>sizeof($added_lemmas)]) }}</p>
            @foreach ($added_lemmas as $add_lemma) 
            <a href="/dict/lemma/{{$add_lemma->id}}">{{$add_lemma->lemma}}</a><br>
            @endforeach
            <br>
        @endif
        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>

        @if ($numAll)
        <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th></th>
                <th>{{ trans('dict.lemma') }}</th>
                <th>{{ trans('dict.new_lemma') }}</th>
                <th>{{ trans('dict.pos') }}</th>
                <th>{{ trans('dict.interpretation') }}</th>
            </tr>
        </thead>
            @foreach($lemmas as $lemma)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td><input type='checkbox' name="lemmas[]" value="{{$lemma->id}}"></td>
                <td data-th="{{ trans('dict.lemma') }}"><a href="/dict/lemma/{{$lemma->id}}">{{$lemma->lemma}}</a></td>
                <td data-th="{{ trans('dict.new_lemma') }}">
                    @include('widgets.form.formitem._text',
                            ['name' => 'new_lemmas['.$lemma->id.']',
                            'value' => $lemma->stemAffixForm(),
                            'special_symbol' => true])                               
                </td>                
                <td data-th="{{ trans('dict.pos') }}">
                    @if($lemma->pos)
                        {{$lemma->pos->name}}
                        @include('dict.lemma.show.features')
                    @endif
                </td>
                <td data-th="{{ trans('dict.interpretation') }}">
                    @foreach ($lemma->getMultilangMeaningTexts() as $meaning_string) 
                        {{$meaning_string}}<br>
                    @endforeach
                </td>
            </tr>
            @endforeach
        </table>
        
            @include('widgets.form.formitem._submit', ['name'=>'copy_lemmas', 'title' => trans('messages.copy')])
            {!! $lemmas->appends($url_args)->render() !!}            
        @endif
{!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    selectWithLang('.select-wordform-dialect', "/dict/dialect/list", 'lang_id', '{{ trans('dict.select_dialect') }}');    
@stop


