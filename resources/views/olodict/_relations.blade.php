@foreach ($relations as $relation_id => $relation_name)
    <?php
        $rel_lemmas = [];
        foreach ($meaning->getLemmaRelation($relation_id, $label_id) as $relation_lemma_id => $relation_lemma) {
            $rel_lemmas[] = '<a href="'. LaravelLocalization::localizeURL('/olodict').args_replace($url_args, 'search_lemma', $relation_lemma).'">'.
                            $relation_lemma.'</a>';
        }
        foreach ($meaning->getLemmaRelation($relation_id, $label_id,0) as $relation_lemma_id => $relation_lemma) {
            $rel_lemmas[] = $relation_lemma;
        }
        
        $relation_meanings =  join ('; ',$rel_lemmas);
    ?>
    @if (sizeof($rel_lemmas)) 
    <p><b>{{$relation_name}}:</b> {!! $relation_meanings !!}</p>
    @endif
@endforeach
