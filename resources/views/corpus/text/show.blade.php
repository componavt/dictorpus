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
            
        @if (user_corpus_edit())
            | @include('widgets.form.button._edit', ['route' => '/corpus/text/'.$text->id.'/edit'])
{{--            | <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id.'/markup') }}{{$args_by_get}}">{{ trans('corpus.re-markup') }}</a>        --}}    
            @if (!$text->hasImportantExamples())
            | @include('widgets.form.button._delete', ['route' => 'text.destroy', 'args'=>['id' => $text->id]]) 
            @endif
            | <a href="{{ LaravelLocalization::localizeURL('/corpus/text/create') }}{{$args_by_get}}">{{ trans('messages.create_new_m') }}</a>
        @else
            | {{ trans('messages.edit') }} | {{ trans('messages.delete') }} | {{ trans('messages.create_new_m') }}
        @endif 
            | <a href="/corpus/text/{{ $text->id }}/history{{$args_by_get}}">{{ trans('messages.history') }}</a>
            | <a href="{{ LaravelLocalization::localizeURL('/help/text/show') }}">? {{ trans('navigation.help') }}</a>            
        </p>
        
        <h2>
            {{ $text->authorsToString() ? $text->authorsToString().'.' : '' }}
            {{ $text->title }}
        </h2>
        
        @if ($text->video && $text->video->youtube_id)
        <div class="row">
            <div class="col-sm-6">
        @endif
        
        @include('corpus.text.show.metadata')
        
        @if ($text->video && $text->video->youtube_id)
            </div>
            <div class="col-sm-6">
                @include('widgets.youtube',
                        ['width' => '100%',
                         'height' => '270',
                         'video' => $text->video->youtube_id
                        ])
            </div>
        </div>
        @endif

        <div class="row corpus-text">
            <div class="col-sm-{{$text->transtext ? '6' : '12'}}">
            @include('corpus.text.show.text')
        @if ($text->transtext)
            </div>
                
            <div class="col-sm-6">
            @include('corpus.text.show.transtext')
        @endif      
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
    highlightSentences();
    
{{-- show/hide a block with meanings and gramsets --}}
    showLemmaLinked({{$text->id}}); 
    
    addWordform('{{$text->id}}','{{$text->lang_id}}');
    posSelect(false);
    checkLemmaForm();
    toggleSpecial();    
@stop

