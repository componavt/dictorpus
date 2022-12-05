@include('corpus.sentence._text_link', ['wid_for_link' => http_build_query(['search_wid'=>$words])])
<ul> 
    @foreach ($sentences as $sentence)        
    <div style='padding: 5px 0'>
        @include('corpus.sentence.view',[
            'marked_words' => $words, 
            'with_left_context' => true,
            'with_right_context' => true]) 
{{--,
            'count' => $sentence_id]--}}            
    </div>
    @endforeach
</ul>