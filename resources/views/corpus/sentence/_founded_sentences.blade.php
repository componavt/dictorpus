<?php $words = $text->getWords($url_args['words']); 
      $wid_for_link = http_build_query(['search_wid'=>$words]);

      $sentences = $text->getSentencesByGram($url_args['words']);
?>
@include('corpus.sentence._text_link')
<ul> 
    @foreach ($sentences as $sentence)        
    <div style='padding: 10px 0'>
        @include('corpus.sentence.view',[
            'marked_words' => $words, 
            'with_left_context' => true,
            'with_right_context' => true,
            'count' => $sentence->id])            
    @endforeach
    </div>
</ul>