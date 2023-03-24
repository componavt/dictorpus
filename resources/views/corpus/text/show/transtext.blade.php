@if ($text->transtext->title)
    <h4>
    @if ($text->transtext->authors)
        {{$text->transtext->authorsToString()}}</h4>
    @endif
    <h3>{!!highlight($text->transtext->title, $url_args['search_w'], 'search-word')!!}</h3>
    <h5>
        {{ $text->transtext->lang->name }}
    </h5>
@endif      
@if ($text->transtext->text)
<?php
    $markup_text = $text->transtext->text_xml 
                ? str_replace("<s id=\"","<s class=\"trans_sentence\" id=\"transtext_s", 
                        mb_ereg_replace('[¦^]', '', $text->transtext->text_xml)) 
                : nl2br(mb_ereg_replace('[¦^]', '', $text->transtext->text)); ?>
        <div id="transtext">{!! highlight($markup_text, $url_args['search_w'], 'search-word') !!}</div>
@endif      
