                @foreach ($meaning_relations as $relation_name => $relation_lemmas)
                <?php
                    $rel_lemmas = [];
                    foreach ($relation_lemmas as $relation_lemma_id => $relation_lemma) {
                        $rel_lemmas[] = '<a href="'. LaravelLocalization::localizeURL('/olodict').args_replace($url_args, 'search_lemma', $relation_lemma).'">'.
                                        $relation_lemma.'</a>';
                    }
                    $relation_meanings =  join ('; ',$rel_lemmas);
                ?>
                <p><b>{{$relation_name}}:</b> {!! $relation_meanings !!}</p>
                @endforeach
