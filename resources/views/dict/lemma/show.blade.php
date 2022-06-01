@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/essential_audio.css')!!}
    {!!Html::style('css/essential_audio_circle.css')!!}
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>

        @if (User::checkAccess('dict.edit'))
            | @include('widgets.form.button._delete', 
                       ['route' => 'lemma.destroy', 
                        'args'=>['id' => $lemma->id]]) 
            | <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}{{$args_by_get}}">{{ trans('messages.create_new_f') }}</a>
        @else
            | {{ trans('messages.edit') }} | {{ trans('messages.delete') }}
        @endif

            | <a href="/dict/lemma/{{ $lemma->id }}/history{{$args_by_get}}">{{ trans('messages.history') }}</a>
        </p>

        @include('dict.lemma.show._title')
        
        @include('dict.lemma.show._props')

        @include('dict.lemma.show._meanings')

        @if ($lemma->isChangeable() || sizeof($lemma->wordforms)>0) 
            @include('dict.lemma_wordform._show')
        @endif
            
        @include('dict.lemma._modal_delete')
@stop

@section('footScriptExtra')
    {!!Html::script('js/essential_audio.js')!!}
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/lemma.js')!!}
    {!!Html::script('js/meaning.js')!!}
    {!!Html::script('js/text.js')!!}
    {!!Html::script('js/wordform.js')!!}
@stop

@section('jqueryFunc')
    loadWordforms({{$lemma->id}});
    @foreach ($lemma->meanings as $meaning)
        loadExamples('{{LaravelLocalization::localizeURL('/dict/meaning/examples/load')}}', {{$meaning->id}}, 0, {{$update_text_links}});
    @endforeach
    
    chooseDialectForGenerate({{$lemma->id}});
    recDelete('{{ trans('messages.confirm_delete') }}');
    showLemmaLinked();    
    
    showAudioInfo();
    
{{-- show/hide a block with lemmas --}}
    showWordBlock('{{LaravelLocalization::getCurrentLocale()}}'); 
    $("#toggle-phrases").click(function() {
        $("#lemma-phrases").toggle();
    });
@stop

