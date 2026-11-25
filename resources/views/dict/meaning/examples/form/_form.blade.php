<form method="GET" action="{{ $form_url }}" class="meaning-example-form">
    @if ($synset_id !== null)
        <input type="hidden" name="synset_id" value="{{ $synset_id }}">
    @endif

    @if (!empty($matrix_mode))
        @include('dict.synset._candidate_members')
    @endif

    <div class="example-word-columns">
        <div class="example-word-column">
            <fieldset class="example-form-section">
                <legend>{{ trans('meaning_examples.source_section') }}</legend>
                @include('dict.meaning.examples.form._source')
            </fieldset>

            <fieldset class="example-form-section example-form-section-plain">
                <legend>{{ trans('meaning_examples.corpuses') }}</legend>
                @include('dict.meaning.examples.form._corpuses')
            </fieldset>
        </div>

        <div class="example-word-column">
            <fieldset class="example-form-section example-partner-section">
                <legend>{{ trans('meaning_examples.partner_section') }}</legend>
                @include('dict.meaning.examples.form._partner')
            </fieldset>

            <fieldset class="example-form-section example-form-section-plain example-combination-section"
                      @if (empty($candidate_mode) && empty($partner) && (int) request()->input('partner_pos_id', 0) === 0) style="display: none;" @endif>
                <legend>{{ trans('meaning_examples.combination_section') }}</legend>
                @include('dict.meaning.examples.form._conditions')
            </fieldset>

            @if (isset($example_status) && empty($candidate_mode) && empty($matrix_mode))
            <fieldset id="example-review-filter" class="example-form-section example-form-section-plain" @if (empty($partner)) style="display: none;" @endif>
                <legend>{{ trans('collocation_examples.review_status') }}</legend>
                <div class="form-group">
                    <select name="example_status" id="example-status" class="form-control" aria-describedby="example-status-hint" @if (empty($partner)) disabled @endif>
                        @foreach (['all', 'unreviewed', 'attached', 'rejected'] as $status_value)
                        <option value="{{ $status_value }}" {{ $example_status === $status_value ? 'selected' : '' }}>
                            {{ trans('collocation_examples.filter_'.$status_value) }}
                        </option>
                        @endforeach
                    </select>
                    <p id="example-status-hint" class="help-block">{{ trans('collocation_examples.review_filter_hint') }}</p>
                </div>
            </fieldset>
            @endif
        </div>
    </div>

    @include('dict.meaning.examples.form._actions')
</form>