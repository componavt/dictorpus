@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/essential_audio.css')!!}
    {!!Html::style('css/essential_audio_circle.css')!!}
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/text.css')!!}
    {!!Html::style('css/mic.css')!!}
@stop

@section('body')
        @include('widgets.modal',['name'=>'modalCreatePhonetic',
                              'title'=>trans('dict.create_phonetic'),
                              'form_url' => LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id.'/create_phonetic').$args_by_get,
                              'submit_title' => trans('messages.create'),
                              'modal_view'=>'dict.lemma.form._create_phonetic'])
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>

        @if (User::checkAccess('dict.edit') && !$lemma->labels()->whereIn('id',[3,5])->count())
            | @include('widgets.form.button._delete', 
                       ['route' => 'lemma.destroy', 
                        'args'=>['id' => $lemma->id]]) 
        @else
            | {{ trans('messages.delete') }}
        @endif

        @if (User::checkAccess('dict.edit'))
            | <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}{{$args_by_get}}">{{ trans('messages.create_new_f') }}</a>
            | <a href="#" onClick="callCreatePhonetic()">{{ trans('dict.create_phonetic') }}</a>
        @else
            | {{ trans('messages.edit') }}
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
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/lemma.js')!!}
    {!!Html::script('js/meaning.js')!!}
    {!!Html::script('js/text.js')!!}
    {!!Html::script('js/wordform.js')!!}
    {!!Html::script('js/mic.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    loadWordforms({{$lemma->id}}, 'load?search_w={{$url_args['search_w']}}');
    @foreach ($lemma->meanings as $meaning)
        loadExamples('{{LaravelLocalization::localizeURL('/dict/meaning/examples/load')}}', {{$meaning->id}}, 0, {{$update_text_links}});
        loadPhoto('meaning', {{$meaning->id}}, '/dict/meaning/{{$meaning->id}}/photo');
    @endforeach
    
    chooseDialectForGenerate({{$lemma->id}});
    recDelete('{{ trans('messages.confirm_delete') }}');
//    showLemmaLinked();    
    
    showAudioInfo();
    
{{-- show/hide a block with lemmas --}}
    showWordBlock('{{LaravelLocalization::getCurrentLocale()}}'); 
    $("#toggle-phrases").click(function() {
        $("#lemma-phrases").toggle();
    });
    
    @if (User::checkAccess('dict.edit'))
    recordAudio('{{$informant_id}}', '{{ csrf_token() }}');
    @endif
@stop

