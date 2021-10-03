<?php $words = $text->getWords($url_args['words']); 
      $wid_for_link = http_build_query(['search_wid'=>$words]);

      $sentences = $text->getSentencesByGram($url_args['words']);
?>
@include('corpus.sentence._text_link')
<ul> 
    @foreach ($sentences as $sentence)
    <li>
        @include('corpus.sentence.view',[
            'sentence_obj' => $sentence,
            'sentence_xml' => $sentence->text_xml, 
            'marked_words' => $words, 
            'count' => $sentence->id,
            'for_view' => true])
    </li>
    @endforeach
</ul>