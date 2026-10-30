<div class="meaning-examples-header">
    <p>
        <a href="{{ $back_to_url }}">{{ trans('meaning_examples.back_to_view') }}</a>

        @if ($synset_id !== null)
            | <a href="{{ LaravelLocalization::localizeURL('/dict/synset/'.$synset_id.'/edit').'#synset-collocations' }}">
                {{ trans('collocations.back_to_synset', ['num' => $synset_id]) }}
            </a>
        @endif

        | <a href="{{ LaravelLocalization::localizeURL('/dict/lemma') }}">
            {{ trans('messages.back_to_list') }}
        </a>
    </p>

    <div class="example-header-columns">
        <div class="example-header-lemma">
            @include('dict.lemma.show._title', [
                'lemma' => $lemma,
                'url_args' => ['search_w' => ''],
            ])

            @include('dict.lemma.show._basic_props')
        </div>

        <div class="example-header-meaning">
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
    </div>
</div>