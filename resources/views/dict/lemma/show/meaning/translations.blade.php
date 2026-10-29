            @if (isset($translation_values[$meaning->id]))
            <div class="show-meaning-translation">
            <p class="meaning-translations-title">
                <strong>{{ trans('dict.translation') }}</strong>
            </p>
                @foreach ($translation_values[$meaning->id] as $lang_text => $translation_lemmas)
                <?php
                    $transl_lemmas = [];
                    foreach ($translation_lemmas as $translation_lemma_id => $translation_lemma_info) {
                        $transl_lemmas[$translation_lemma_info['lemma']] = '<a href="/dict/lemma/'.$translation_lemma_id.'">'.
                                        $translation_lemma_info['lemma'].'</a>';
                    }
                    ksort($transl_lemmas);
                    $translation_meanings =  join ('; ',$transl_lemmas);
                ?>
                <p><em>{{$lang_text}}:</em> {!! $translation_meanings !!}</p>
                @endforeach
            </div>
            @endif
