@extends('layouts.page')

@section('page_title')
{{ trans('navigation.texts') }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('body')
        @include('widgets.modal',['name'=>'modalAddWordform',
                                  'title'=>trans('corpus.add-wordform'),
                                  'submit_id' => 'save-wordform',
                                  'submit_title' => trans('messages.save'),
                                  'modal_view'=>'dict.lemma._form_create_wordform'])
        
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>
            
        @if (User::checkAccess('corpus.edit'))
            | @include('widgets.form._button_edit', ['route' => '/corpus/text/'.$text->id.'/edit'])
            | <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id.'/markup') }}{{$args_by_get}}">{{ trans('corpus.re-markup') }}</a>            
            | @include('widgets.form._button_delete', ['route' => 'text.destroy', 'id' => $text->id]) 
        @else
            | {{ trans('messages.edit') }} | {{ trans('messages.delete') }}
        @endif 
            | <a href="/corpus/text/{{ $text->id }}/history{{$args_by_get}}">{{ trans('messages.history') }}</a>
        </p>
        
        <h2>{{ $text->title }}</h2>
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
        
        <div class="row corpus-text">
            <div class="col-sm-6">
        @if ($text->title)
                    <h4>{{ $text->title }}<br>
                    ({{ $text->lang->name }})</h4>
        @endif      
        
        @if ($text->text)
        <?php   if ($text->text_xml) :
                    $markup_text = $text->setLemmaLink($text->text_xml);
                    $markup_text = str_replace("<s id=\"","<s class=\"sentence\" id=\"text_s",$markup_text);
                else :
                    $markup_text = nl2br($text->text);
                endif; 
        ?>
                    <div id="text">{!! $markup_text !!}</div>
        @endif      
            </div>
                
        @if ($text->transtext)
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
        @endif      
            </div>
        </div>
        
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/meaning.js')!!}
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/text.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}', '/corpus/text');
    highlightSentences();
    addWordMeaning('{{LaravelLocalization::localizeURL('/corpus/text/add/example')}}');
    showLemmaLinked();
    addWordform('{{$text->id}}','{{$text->lang_id}}');
    
@stop

