@extends('layouts.page')

@section('page_title')
{{ trans('navigation.texts') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
    {!! css('text')!!}
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('body')
        @include('corpus.text.modals_for_markup')
        @include('widgets.modal',['name'=>'modalOpenBigPhoto',
                              'title'=>$text->event && $text->event->place ? $text->event->place->placeString() : ''
])
        
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>
            
        @if (user_corpus_edit())
{{--            | <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id.'/markup') }}{{$args_by_get}}">{{ trans('corpus.re-markup') }}</a>        --}}    
            | @include('widgets.form.button._edit', 
                     ['route' => '/corpus/text/'.$text->id.'/sentences',
                      'link_text' => ' '.mb_strtolower(trans('corpus.sentences'))])
            | @include('widgets.form.button._edit', 
                     ['route' => '/corpus/text/'.$text->id.'/photos',
                      'link_text' => ' '.mb_strtolower(trans('corpus.photos_edit'))])
            @if (!$text->hasImportantExamples())
            | @include('widgets.form.button._delete', ['route' => 'text.destroy', 'args'=>['id' => $text->id]]) 
            @endif
            | <a href="{{ LaravelLocalization::localizeURL('/corpus/text/create') }}{{$args_by_get}}">{{ trans('messages.create_new_m') }}</a>
        @else
            | {{ trans('messages.edit') }} | {{ trans('messages.delete') }} | {{ trans('messages.create_new_m') }}
        @endif 
            | <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'. $text->id) }}/history{{$args_by_get}}">{{ trans('messages.history') }}</a>
            | <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id.'/stats') }}">{{ trans('navigation.stats') }}</a>            
            | <a href="{{ LaravelLocalization::localizeURL('/help/text/show') }}">? {{ trans('navigation.help') }}</a>            
        </p>
        
        <h2>
            {{ $text->authorsToString() ? $text->authorsToString().'.' : '' }}
            {!!highlight($text->title, $url_args['search_w'], 'search-word')!!}
        @if (user_corpus_edit())
            @include('widgets.form.button._edit', ['route' => '/corpus/text/'.$text->id.'/edit', 'without_text'=>1])
        @endif 
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

    @if ($text->cyrtext)
            @include('corpus.text.show.3_columns')
    @else
        <div class="row corpus-text">
            <div class="col-sm-{{$text->transtext ? '6' : '12'}}">
            @include('corpus.text.show.text')
            </div>
        @if ($text->transtext)               
            <div class="col-sm-6">
            @include('corpus.text.show.transtext')
            </div>
        @endif      
        </div>
    @endif      
        
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
    toggleColumns();
@stop

