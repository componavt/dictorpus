<form method="GET" action="{{ $form_url }}" class="meaning-example-form">
    @if ($synset_id !== null)
        <input type="hidden" name="synset_id" value="{{ $synset_id }}">
    @endif

    @if (!empty($matrix_mode))
        @include('dict.synset._candidate_members')
    @endif

    <div class="example-word-columns">
        <fieldset class="example-form-section">
            <legend>{{ trans('meaning_examples.source_section') }}</legend>
            @include('dict.meaning.examples.form._source')
        </fieldset>

        <fieldset class="example-form-section">
            <legend>{{ trans('meaning_examples.partner_section') }}</legend>
            @include('dict.meaning.examples.form._partner')
        </fieldset>
    </div>

    <fieldset class="example-form-section example-combination-section"
            @if (empty($candidate_mode) && empty($partner)) style="display: none;" @endif>
        <legend>{{ trans('meaning_examples.combination_section') }}</legend>
        @include('dict.meaning.examples.form._conditions')
    </fieldset>

    <fieldset class="example-form-section">
        <legend>{{ trans('meaning_examples.corpuses') }}</legend>
        @include('dict.meaning.examples.form._corpuses')
    </fieldset>

    @include('dict.meaning.examples.form._actions')
</form>