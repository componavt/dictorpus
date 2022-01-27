@if ($text->transtext->title)
    <h4>
    @if ($text->transtext->authors)
        {{$text->transtext->authorsToString()}}<br>
    @endif
        {{ $text->transtext->title }}<br>
        ({{ $text->transtext->lang->name }})
    </h4>
@endif      
@if ($text->transtext->text)
<?php
    $markup_text = $text->transtext->text_xml 
                ? str_replace("<s id=\"","<s class=\"trans_sentence\" id=\"transtext_s", 
                        mb_ereg_replace('[¦^]', '', $text->transtext->text_xml)) 
                : nl2br(mb_ereg_replace('[¦^]', '', $text->transtext->text)); ?>
        <div id="transtext">{!! $markup_text !!}</div>
@endif      
