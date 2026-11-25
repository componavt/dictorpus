<div class="example-source-info">
    @include('dict.lemma.show._title', [
        'lemma' => $lemma,
        'url_args' => ['search_w' => ''],
    ])

    @include('dict.lemma.show._basic_props')

    <h3>
        {{ $source->meaning_n }} {{ trans('dict.meaning') }}
        @if (count($meaning_labels))
            ({{ implode(', ', $meaning_labels) }})
        @endif
    </h3>

    @if ($source->meaningTexts->count())
        <ul>
            @foreach ($source->meaningTexts as $meaning_text)
                @if ($meaning_text->meaning_text)
                    <li>
                        @if ($meaning_text->lang)
                            <em>{{ $meaning_text->lang->name }}:</em>
                        @endif
                        {{ $meaning_text->meaning_text }}
                    </li>
                @endif
            @endforeach
        </ul>
    @endif
</div>