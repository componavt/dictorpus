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
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>
            | <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}">{{ trans('messages.back_to_show') }}</a>            
        @if (user_corpus_edit())
            | @include('widgets.form.button._edit', ['route' => '/corpus/text/'.$text->id.'/edit'])
        @else
            | {{ trans('messages.edit') }}
        @endif 
        </p>
        
        <h2>
            {{ $text->authorsToString() ? $text->authorsToString().'.' : '' }}
            {{ $text->title }}
        </h2>
        
        <table class="table-bordered table-striped table-wide rwd-table wide-md">
        @for ($i=1; $i<=$total; $i++)
        <tr>
            <td>{{$i}}</td>
            <td>
                <div id="sentence-{{$i}}">
                {!! $text->cyrToSentence($sentences[$i] ?? '', $cyr_sentences[$i]['words'] ?? '') !!}
                </div>
            </td>
                        
            <td>
                {!! $trans_sentences[$i]['sentence'] ?? '' !!}
            </td>
        </tr>
        @endfor     
        </table>
        <div class="row">
            <div class="col-sm-6">
                <a href="/corpus/text/{{ $text->id }}/markup{{$args_by_get}}" class="btn btn-primary btn-default">Продолжить размечать текст</a>
            </div>
            <div class="col-sm-6" style="text-align: right">
                <a href="/corpus/text/{{ $text->id }}/edit{{$args_by_get}}" class="btn btn-primary btn-default">Вернуться и исправить текст</a>
            </div>
        </div>
        
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
    
    toggleSpecial();  
    
@stop

