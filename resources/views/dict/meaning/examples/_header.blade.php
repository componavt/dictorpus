<div class="meaning-examples-header">
    @if ($synset_id !== null)
        @include('dict.synset._nav', ['active' => 'examples', 'vepkar_lemma' => $lemma, 'vepkar_meaning_id' => $source->id, 'help_section' => $help_section ?? 'synset-help-examples'])
    @else
    <p>
        <a href="{{ $back_to_url }}">{{ trans('meaning_examples.back_to_view') }}</a>
        | <a href="{{ LaravelLocalization::localizeURL('/dict/lemma') }}">{{ trans('messages.back_to_list') }}</a>
    </p>
    @endif

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