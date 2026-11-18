<form method="POST"
      class="example-collocation-form"
      action="{{ LaravelLocalization::localizeURL('/dict/meaning/'.$meaning->id.'/examples/collocation') }}"
      data-dictionary-gloss="{{ $default_display_gloss }}">
    {!! csrf_field() !!}
    <input type="hidden" name="text_id" value="{{ $source_word->text_id }}">
    <input type="hidden" name="source_w_id" value="{{ $source_word->w_id }}">
    <input type="hidden" name="partner_w_id" value="{{ $partner_word->w_id }}">
    <input type="hidden" name="collocate_meaning_id" value="{{ $partner->id }}">

    @if ($synset_id !== null)
        <input type="hidden" name="synset_id" value="{{ $synset_id }}">
    @endif

    <div class="example-collocation-form-errors alert alert-danger"
         role="alert" style="display: none;"></div>

    <div class="form-group">
        <label>{{ trans('example_collocations.source') }}</label>
        <p>
            {{ $meaning->lemma->lemma }}<sup>{{ $meaning->meaning_n }}</sup>
            — {{ $meaning->getMultilangMeaningTextsStringLocale() }}
        </p>
    </div>

    <div class="form-group">
        <label>{{ trans('collocations.partner') }}</label>
        <p>
            {{ $partner->lemma->lemma }}<sup>{{ $partner->meaning_n }}</sup>
            — {{ $partner->getMultilangMeaningTextsStringLocale() }}
        </p>
    </div>

    <p class="text-muted">
        {{ trans('example_collocations.example_pair', [
            'source' => $source_word->word,
            'partner' => $partner_word->word,
            'from' => $source_word->word_number,
            'to' => $partner_word->word_number,
        ]) }}
    </p>

    @include('dict.collocation._details_fields')

    <button type="submit" class="btn btn-primary">
        {{ trans('collocations.save') }}
    </button>
    <button type="button" class="btn btn-default" data-dismiss="modal">
        {{ trans('collocations.cancel') }}
    </button>
</form>
