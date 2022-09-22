    <div><a class="arrow up" onClick="prevLemmas()" title="Предыдущие слова"></a></div>
    @foreach ($lemma_list as $lemma)
    <p><a class="{{$url_args['search_lemma'] == $lemma->lemma ? 'lemma-active' : '' }}" href="/olodict?search_lemma={{$lemma->lemma}}">{{$lemma->lemma}}</a></p>
    @endforeach
    <div><a class="arrow down" onClick="nextLemmas()" title="Следующие слова"></a></div>
