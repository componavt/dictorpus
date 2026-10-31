<div class="example-form-actions">
@if (empty($matrix_mode))
    <div class="example-page-size">
        <label for="example-search-per-page">
            {{ trans($candidate_mode ? 'collocation_candidates.per_page' : 'meaning_examples.per_page') }}
        </label>
        <select id="example-search-per-page" name="per_page" class="form-control">
            @foreach (array_unique([5, 10, 20, 25, 50, 100, $form_values['per_page']]) as $value)
                <option value="{{ $value }}" @if ($form_values['per_page'] == $value) selected @endif>{{ $value }}</option>
            @endforeach
        </select>
    </div>
@endif

    <button type="submit" class="btn btn-primary">
        {{ trans($candidate_mode ? 'collocation_candidates.apply' : 'meaning_examples.submit') }}
    </button>

    @if (!$candidate_mode)
        <button type="submit" class="btn btn-default"
                formaction="{{ LaravelLocalization::localizeURL('/dict/meaning/'.$source->id.'/examples/candidates') }}">
            {{ trans('collocation_candidates.title') }}
        </button>
    @endif

    <a class="btn btn-default"
       href="{{ $form_url.($synset_id !== null ? '?'.http_build_query(['synset_id' => $synset_id]) : '') }}">
        {{ trans('meaning_examples.reset_filters') }}
    </a>
</div>