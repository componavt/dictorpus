<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.inflexion_groups') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/table.css')!!}
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('body')        
        @include('dict.reverse_lemma._search_form_with_dialect',
                ['url' => '/dict/reverse_lemma/inflexion_groups']) 

        @if (sizeof($groups))
        <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                @foreach($gramset_heads as $gramset)
                <th>{{ $gramset }}</th>
                @endforeach
                <th>{{ trans('messages.total') }}</th>
                <th>{{ trans('navigation.lemmas') }}</th>
            </tr>
        </thead>
            @foreach($groups as $inflexion_str => $lemmas)
            <?php $inflexions = preg_split("/\_/u",$inflexion_str);
                  $inflexion_count = 0; ?>
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                @foreach($gramset_heads as $gramset)
                <td data-th="{{ $gramset }}">
                    {{ $inflexions[$inflexion_count++] }}
                </td>
                @endforeach
                <td data-th="{{ trans('messages.total') }}">
                    {{ sizeof($lemmas) }}
                </td>
                <td data-th="{{ trans('navigation.lemmas') }}">
                @foreach ($lemmas as $lemma_id => $lemma)
                    <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma_id)}}{{$args_by_get}}">{{$lemma}}</a>
                @endforeach
                </td>
            </tr>
            @endforeach
        </table>
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/select2.min.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    
    $(".select-dialect").select2({
        width: '100%',
        ajax: {
          url: "/dict/dialect/list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
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


