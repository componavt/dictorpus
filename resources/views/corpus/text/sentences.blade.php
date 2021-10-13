@extends('layouts.page')

@section('page_title')
{{ trans('navigation.texts') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/text.css')!!}
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('body')
        @include('corpus.text.modals_for_markup')
        
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>
            | <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}">{{ trans('messages.back_to_show') }}</a>            
        </p>
        
        <h2>
            {{ $text->authorsToString() ? $text->authorsToString().'.' : '' }}
            {{ $text->title }}
        </h2>
        
        <table class="table-bordered table-striped table-wide rwd-table wide-md">
        @foreach ($sentences as $sentence)
        <tr>
            <td>{{$sentence->s_id}}</td>
            <td>
                <img class="img-loading" id="loading-sentence-{{$sentence->id}}" src="{{ asset('images/loading.gif') }}">
                <div id="sentence-{{$sentence->id}}">
                @include('corpus.sentence.show', ['with_edit' => true])
                </div>
            </td>
            <td>
                <i id="sentence-edit-{{$sentence->id}}" class="sentence-edit fa fa-pencil-alt fa-lg" data-sid="{{$sentence->id}}"></i>                
                <i class="fa fa-sync-alt fa-lg markup-sentence" title="сбросить все связи и разметить заново" onclick="markupSentence({{$sentence->id}})"></i>
            </td>
            <td>
                {!! $trans_sentences[$sentence->s_id] ?? '' !!}
            </td>
        </tr>
        @endforeach     
        </table>
        
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    
    {!!Html::script('js/lemma.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/meaning.js')!!}
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/text.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
    highlightSentences();
    
{{-- show/hide a block with meanings and gramsets --}}
    showLemmaLinked({{$text->id}}); 
    
    addWordform('{{$text->id}}','{{$text->lang_id}}');
    posSelect(false);
    checkLemmaForm();
    toggleSpecial();  
    
    $(".sentence-edit").click(function() {
        var sid=$(this).data('sid');
        loadSentenceForm(sid);
    });    
@stop

