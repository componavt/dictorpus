@if (count($filter_values['source_gramsets']))
    <div class="form-group">
        <label for="example-source-gramsets">{{ trans('meaning_examples.source_gramsets') }}</label>
        <select id="example-source-gramsets" name="source_gramset_ids[]" multiple
                class="form-control example-extra-select"
                data-placeholder="{{ trans('meaning_examples.all_forms') }}">
            @foreach ($filter_values['source_gramsets'] as $id => $name)
                <option value="{{ $id }}" @if (in_array((int) $id, $selected_filters['source_gramset_ids'], true)) selected @endif>{{ $name }}</option>
            @endforeach
        </select>
        <p class="help-block">{{ trans('meaning_examples.source_gramsets_hint') }}</p>
    </div>
@endif

<div class="form-group">
    <label for="example-corpuses">{{ trans('meaning_examples.corpuses') }}</label>
    <select id="example-corpuses" name="corpus_ids[]" multiple
            class="form-control example-extra-select"
            data-placeholder="{{ trans('meaning_examples.all_corpuses') }}">
        @foreach ($filter_values['corpuses'] as $id => $name)
            <option value="{{ $id }}" @if (in_array((int) $id, $selected_filters['corpus_ids'], true)) selected @endif>{{ $name }}</option>
        @endforeach
    </select>
    <p class="help-block">{{ trans('meaning_examples.corpuses_hint') }}</p>
</div>