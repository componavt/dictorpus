    <h2>{{trans('olodict.search_by_alphabet')}}</h2>
    
    @if ($url_args['page']>1)
    <div><a class="arrow up" onClick="loadLemmas('{{$locale}}', {{$url_args['page']-1}}, {{$url_args['by_alpha']}})" title="{{trans('olodict.prev_words')}}"></a></div>
    @else 
    <div class="arrow up inactive"></div>
    @endif
    
    <div id="lemma-l">
    @foreach ($lemma_list as $lemma)
    <p><a class="{{$url_args['search_lemma'] == $lemma->lemma ? 'lemma-active' : '' }}" href="/{{$locale}}/olodict{{args_replace($url_args, 'search_lemma', $lemma->lemma)}}">{{$lemma->lemma}}</a></p>
    @endforeach
    </div>

    @if ($lemmas_total > $url_args['page']*$url_args['limit_num'])    
    <div><a class="arrow down" onClick="loadLemmas('{{$locale}}', {{$url_args['page']+1}}, {{$url_args['by_alpha']}})" title="{{trans('olodict.next_words')}}"></a></div>
    @else 
    <div class="arrow down inactive"></div>
    @endif
