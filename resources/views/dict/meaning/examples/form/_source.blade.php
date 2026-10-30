@if (count($filter_values['source_gramsets']))
    <div class="form-group">
        <label for="example-source-gramsets">{{ trans('meaning_examples.source_gramsets') }}</label>
        <select id="example-source-gramsets" name="source_gramset_ids[]" multiple
                class="form-control example-extra-select"
                data-placeholder="{{ trans('meaning_examples.all_forms') }}">
            @foreach ($filter_values['source_gramsets'] as $id => $name)
                <option value="{{ $id }}" @if (in_array((int) $id, $form_values['source_gramset_ids'], true)) selected @endif>{{ $name }}</option>
            @endforeach
        </select>
        <p class="help-block">{{ trans('meaning_examples.source_gramsets_hint') }}</p>
    </div>
@endif

@include('dict.meaning.examples.form._checkbox', [
    'field' => 'source_only_checked',
    'caption' => 'source_only_checked',
])