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
            
        @if (User::checkAccess('corpus.edit'))
            | @include('widgets.form.button._edit', ['route' => '/corpus/text/'.$text->id.'/edit'])
            | <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id.'/markup') }}{{$args_by_get}}">{{ trans('corpus.re-markup') }}</a>            
            | @include('widgets.form.button._delete', ['route' => 'text.destroy', 'args'=>['id' => $text->id]]) 
            | <a href="{{ LaravelLocalization::localizeURL('/corpus/text/create') }}{{$args_by_get}}">{{ trans('messages.create_new_m') }}</a>
        @else
            | {{ trans('messages.edit') }} | {{ trans('messages.delete') }} | {{ trans('messages.create_new_m') }}
        @endif 
            | <a href="/corpus/text/{{ $text->id }}/history{{$args_by_get}}">{{ trans('messages.history') }}</a>
            | <a href="{{ LaravelLocalization::localizeURL('/help/text/show') }}">? {{ trans('navigation.help') }}</a>            
        </p>
        
        <h2>
            {{ $text->authors ? $text->authorsToString().'.' : '' }}
            {{ $text->title }}
        </h2>
        
        @if ($text->video && $text->video->youtube_id)
        <div class="row">
            <div class="col-sm-6">
        @endif
        
        <h3>{{ trans('corpus.corpus') }}: {{ $text->corpus->name }}</h3>
        <p><i>{{ $labels }}</i></p>

        @if ($text->event)
        <p> 
            @include('corpus.event._to_string',['event'=>$text->event, 'lang_id' => $text->lang_id])
        </p>
        @endif
        
        @if ($text->source)
        <p> 
            @include('corpus.source._to_string',['source'=>$text->source])
        </p>
        @endif
        
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

        @if ($text->transtext)
        <div class="row corpus-text">
            <div class="col-sm-6">
        @endif
        @if ($text->title)
                    <h4>
                        @if ($text->authors)
                        {{$text->authorsToString()}}<br>
                        @endif
                        {{ $text->title }}<br>
                    ({{ $text->lang->name }})</h4>
        @endif      
        
        @if ($text->text)
        <?php   if ($text->text_xml) :
                    $markup_text = $text->setLemmaLink($text->text_xml, 
                            $url_args['search_word'] ?? null, $url_args['search_sentence'] ?? null,
                            true, $url_args['search_wid'] ?? null);
                else :
                    $markup_text = nl2br($text->text);
                endif; 
        ?>
                    <div id="text">{!! $markup_text !!}</div>
        @endif      
        @if ($text->transtext)
            </div>
                
            <div class="col-sm-6">
            @if ($text->transtext->title)
                    <h4>{{ $text->transtext->title }}<br>
                    ({{ $text->transtext->lang->name }})</h4>
            @endif      
            @if ($text->transtext->text)
            <?php $markup_text = $text->transtext->text_xml 
                            ? str_replace("<s id=\"","<s class=\"trans_sentence\" id=\"transtext_s",$text->transtext->text_xml) 
                            : nl2br($text->transtext->text); ?>
                    <div id="transtext">{!! $markup_text !!}</div>
            @endif      
            </div>
        </div>
        @endif      
        
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

