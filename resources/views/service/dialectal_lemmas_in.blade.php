@foreach ($lemmas as $lemma_id => $info)
    <li class='row-{{ $lemma_id }}'><span data-id='{{ $lemma_id }}' class='check-lemma clickable'>{{ $info['lemma'] }}</span>
        @if (!empty($info['wordforms']))
        ({{ $info['wordforms'] }})
        @endif
        <a class="big_sign" href='{{ route('lemma.show', $lemma_id)}}' target='_blank'>→</a>
    </li>
@endforeach
