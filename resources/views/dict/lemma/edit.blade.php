@extends('layouts.master')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('content')
        <h1>{{ trans('navigation.lemmas') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_lemma')}}: {{ $lemma->lemma}}</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($lemma, array('method'=>'PUT', 'route' => array('lemma.update', $lemma->id))) !!}
        @include('dict.lemma._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit',
                                      'lang_values' => $lang_values, 
                                      'pos_values'  => $pos_values])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
@stop

@section('jqueryFunc')
    $(".multiple-select").select2({
        ajax: {
          url: "/dict/lemma/meanings_list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
//              page: params.page
              lang_id: {{ $lemma->lang_id}},
              pos_id: $( "#lemma_pos_id option:selected" ).val(), {{-- $lemma->pos_id --}}
              lemma_id: {{ $lemma->id}}
            };
          },
/*          processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
              results: data.items,
              pagination: {
                more: (params.page * 30) < data.total_count
              }
            };
          },*/
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }//,
//        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
  //      minimumInputLength: 1,
    //    templateResult: formatRepo, // omitted for brevity, see the source of this page
      //  templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
      });
@stop
