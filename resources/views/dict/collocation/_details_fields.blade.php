<div class="form-group">
    <label for="concept_category_id">
        {{ trans('collocations.category') }}
    </label>

    <select id="concept_category_id"
            name="concept_category_id"
            class="form-control">
        <option value="">
            {{ trans('collocations.without_category') }}
        </option>

        @foreach ($category_values as $category_id => $category_name)
            <option value="{{ $category_id }}"
                {{ (string) old('concept_category_id', $default_category_id) === (string) $category_id ? 'selected' : '' }}>
                {{ $category_name }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="display_gloss">
        {{ trans('collocations.display_gloss') }}
    </label>

    <textarea id="display_gloss"
              name="display_gloss"
              class="form-control"
              rows="2">{{ old('display_gloss', $default_display_gloss) }}</textarea>

    <button type="button"
            id="use-dictionary-gloss"
            class="btn btn-link">
        {{ trans('collocations.use_dictionary_gloss') }}
    </button>
</div>