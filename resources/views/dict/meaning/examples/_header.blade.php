<div class="meaning-examples-header">
    @if ($synset_id !== null)
        @include('dict.synset._nav', ['active' => 'examples', 'vepkar_lemma' => $lemma, 'vepkar_meaning_id' => $source->id, 'help_section' => $help_section ?? 'synset-help-examples'])
    @else
    <p>
        <a href="{{ $back_to_url }}">{{ trans('meaning_examples.back_to_view') }}</a>
        | <a href="{{ LaravelLocalization::localizeURL('/dict/lemma') }}">{{ trans('messages.back_to_list') }}</a>
    </p>
    @endif
</div>