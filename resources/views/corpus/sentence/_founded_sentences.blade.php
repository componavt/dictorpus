<ul>
    @foreach ($text->getWords($url_args['search_word1'], $url_args['search_word2'], $url_args['search_distance_from'], $url_args['search_distance_to']) as $word)
    <li>
        @include('corpus.sentence.show',['sentence'=>\App\Models\Corpus\Sentence::getBySid($text->id, $word->sentence_id)])
    </li>
    @endforeach
</ul>