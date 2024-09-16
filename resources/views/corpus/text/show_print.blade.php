@extends('layouts.'.($for_print ? 'for_print' : 'page'))

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
        @include('widgets.modal',['name'=>'modalOpenBigPhoto',
                              'title'=>$text->event && $text->event->place ? $text->event->place->placeString() : ''
])
        
        <h2>
            {{ $text->authorsToString() ? $text->authorsToString().'.' : '' }}
            {!!highlight($text->title, $url_args['search_w'], 'search-word')!!}
        </h2>
        
        @if ($text->video && $text->video->youtube_id)
        <div class="row">
            <div class="col-sm-6">
        @endif
        
        @include('corpus.text.show.metadata')
        
        @include('corpus.text.show.photos')
        
        @include('corpus.audiotext._show_files',['audiotexts'=>$text->audiotexts])
        
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
    {!! js('rec-delete-link')!!}
    
    {!! js('lemma') !!}
    {!! js('list_change')!!}
    {!! js('meaning')!!}
    {!! js('select2.min')!!}
    {!! js('special_symbols')!!}
    {!! js('text')!!}
    {!! js('photo')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
    highlightSentences();
    
{{-- show/hide a block with meanings and gramsets --}}
    showLemmaLinked({{$text->id}}); 
    
    addWordform('{{$text->id}}','{{$text->lang_id}}', '{{LaravelLocalization::getCurrentLocale()}}');
    posSelect(false);
    checkLemmaForm();
    toggleSpecial();  
    openBigPhoto('.photo');
@stop

