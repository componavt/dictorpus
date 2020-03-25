            @if (isset($meaning_relations[$meaning->id]))
            <ul>
                @foreach ($meaning_relations[$meaning->id] as $relation_name => $relation_lemmas)
                <?php
                    $rel_lemmas = [];
                    foreach ($relation_lemmas as $relation_lemma_id => $relation_lemma_info) {
                        $rel_lemmas[] = '<a href="/dict/lemma/'.$relation_lemma_id.'">'.
                                        $relation_lemma_info['lemma'].'</a> ('.
                                        $relation_lemma_info['meaning'].')';
                    }
                    if (sizeof($rel_lemmas)>1) {
                        $relation_meanings =  '<br>'. join ('<br>',$rel_lemmas);
                    } else {
                        $relation_meanings =  join ('; ',$rel_lemmas);
                    }                        
                ?>
                <p><b>{{$relation_name}}:</b> {!! $relation_meanings !!}</p>
                @endforeach
            </ul>
            @endif
