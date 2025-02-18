<h4></h4>
@if ($text->cyrtext->title)
    <h3>{!!highlight($text->cyrtext->title, $url_args['search_w'], 'search-word')!!}</h3>
@endif      
@if ($text->cyrtext->text)
<?php
    $markup_text = $text->cyrtext->text_xml 
                ? str_replace("<s id=\"", "<s class=\"cyr_sentence\" id=\"cyrtext_s",
                        str_replace("<w id=\"","<w class=\"cyr_word\" id=\"cyr_w_", 
                            mb_ereg_replace('[¦^]', '', $text->cyrtext->text_xml)))
                : nl2br(mb_ereg_replace('[¦^]', '', $text->cyrtext->text)); ?>
        <div id="cyrtext">{!! highlight(highlight($markup_text, $url_args['search_w']), $url_args['search_text']) !!}</div>
@endif      
