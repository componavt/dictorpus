            @if ($lemma->features->reflexive)
                ({{ trans('dict.reflexive') }})
            @endif
            @if ($lemma->features->transitive)
                ({{ trans('dict.transitive') }})
            @endif
