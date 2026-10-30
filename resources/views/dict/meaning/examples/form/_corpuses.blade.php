<div class="form-group">
    <label class="sr-only" for="example-corpuses">{{ trans('meaning_examples.corpuses') }}</label>
    <select id="example-corpuses" name="corpus_ids[]" multiple
            class="form-control example-extra-select"
            data-placeholder="{{ trans('meaning_examples.all_corpuses') }}">
        @foreach ($filter_values['corpuses'] as $id => $name)
            <option value="{{ $id }}" @if (in_array((int) $id, $form_values['corpus_ids'], true)) selected @endif>{{ $name }}</option>
        @endforeach
    </select>
    <p class="help-block">{{ trans('meaning_examples.corpuses_hint') }}</p>
</div>