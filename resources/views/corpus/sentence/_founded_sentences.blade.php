<?php $words = $text->getWords($url_args['words']); 
$wid_for_link = http_build_query(['search_wid'=>$words->pluck('w_id')->toArray()]);?>

@include('corpus.sentence._text_link')
<ul> 
    @foreach ($words as $word)
    <li><?php $sentence=\App\Models\Corpus\Sentence::getBySid($text->id, $word->sentence_id); ?>
        @include('corpus.sentence.view',[
            'sentence_obj' => $sentence,
            'sentence_xml' => $sentence->text_xml, 
            'marked_words' => [$word->w_id], 
            'count' => $sentence->id,
            'for_view' => true])
    </li>
    @endforeach
</ul>