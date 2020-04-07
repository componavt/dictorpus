            @if (isset($translation_values[$meaning->id]))
            <div class="show-meaning-translation">
            <h4>{{ trans('dict.translation')}}</h4>
                @foreach ($translation_values[$meaning->id] as $lang_text => $translation_lemmas)
                <?php
                    $transl_lemmas = [];
                    foreach ($translation_lemmas as $translation_lemma_id => $translation_lemma_info) {
                        $transl_lemmas[] = '<a href="/dict/lemma/'.$translation_lemma_id.'">'.
                                        $translation_lemma_info['lemma'].'</a>';
                                        //' ('.$translation_lemma_info['meaning'].')';
                    }
                    /*if (sizeof($transl_lemmas)>1) {
                        $translation_meanings =  '<br>'. join ('<br>',$transl_lemmas);
                    } else {*/
                        $translation_meanings =  join ('; ',$transl_lemmas);
                    //}                        
                ?>
                <p><b>{{$lang_text}}:</b> {!! $translation_meanings !!}</p>
                @endforeach
            </div>
            @endif
