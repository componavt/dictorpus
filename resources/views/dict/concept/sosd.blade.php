<?php $total_count =1; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.sosd') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('body')    
        {!! Form::open(['url' => '/dict/concept/sosd', 
                             'method' => 'get']) 
        !!}
<div class="search-form row">
<div class="row">
    <div class="col-md-4">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_lang', 
                 'is_multiple' => false,
                 'values' => $lang_values,
                 'value' => $search_lang,
                 'title' => trans('dict.lang'),
                 'class'=>'multiple-select-lang form-control',
        ])                 
    </div>
    <div class="col-md-4">
        @include('widgets.form.formitem._select2',
                ['name' => 'search_places', 
                 'values' =>$place_values,
                 'value' => $search_places,
                 'title' => trans('dict.meaning_place'),
                 'class'=>'select-places form-control'
            ])
    </div>
    <div class="col-md-4 search-button-b">       
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>                 
        {!! Form::close() !!}

    <p><b>{{trans('dict.lang')}}</b>: {{$search_lang_name}}
        <table class="table table-striped rwd-table wide-lg">
        <thead>
            <tr>
                <th>N</th>
                <th>{{ trans('dict.concept') }}</th>
                @foreach ($search_places as $place_id)
                <th>{{ $place_values[$place_id] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach($concept_lemmas as $concept_text => $place_lemmas)
            <tr>
                <td>{{$total_count++}}.</td>
                <td data-th="{{ trans('dict.concept') }}">{{ $concept_text }}</td>
            @foreach ($place_lemmas as $place_name => $lemmas)
                <td data-th="{{  $place_name }}">
                    <?php $count=0;?>
                    @foreach($lemmas as $lemma_id=>$lemma)
                    <a href="/dict/lemma/{{$lemma_id}}">{{$lemma}}</a>@if($count++<sizeof($lemmas)-1),@endif
                    @endforeach
                </td>
            @endforeach
            </tr>
        @endforeach
        </tbody>
        </table>
    </div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
@stop

@section('jqueryFunc')
    $(".multiple-select-lang").select2();
    
    $('.select-places').select2({
        width: '100%',
        ajax: {
          url: '/corpus/place/list',
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              with_meanings: true,
              lang_id: $( "#search_lang option:selected" ).val()
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    });   

@stop


