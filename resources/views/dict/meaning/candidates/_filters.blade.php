<form method="GET" action="{{ $base_url }}">
    @if (isset($filters['synset_id']))
        <input type="hidden" name="synset_id" value="{{ $filters['synset_id'] }}">
    @endif

    @include('dict.meaning.candidates._pos_filter')

    @include('dict.meaning.examples._checked_filters', [
        'source_only_checked' => $filters['source_only_checked'],
        'only_checked' => $filters['only_checked'],
    ])

    @foreach (['same_gramset', 'whole_sentence'] as $name)
        <input type="hidden" name="{{ $name }}" value="0">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="{{ $name }}" value="1" @if ($filters[$name]) checked @endif>
                {{ trans('meaning_examples.'.$name) }}
            </label>
        </div>
    @endforeach

    <div class="candidate-distance">
        <label for="candidate-distance-from">{{ trans('meaning_examples.distance_from') }}</label>
        <input id="candidate-distance-from" type="number" name="distance_from"
               class="form-control" value="{{ $filters['distance_from'] }}" required>

        <label for="candidate-distance-to">{{ trans('meaning_examples.distance_to_short') }}</label>
        <input id="candidate-distance-to" type="number" name="distance_to"
               class="form-control" value="{{ $filters['distance_to'] }}" required>
    </div>

    <div class="form-group">
        <label for="candidate-per-page">{{ trans('collocation_candidates.per_page') }}</label>
        <select id="candidate-per-page" name="per_page" class="form-control candidate-page-size">
            @foreach (array_unique([10, 20, 25, 50, 100, $filters['per_page']]) as $value)
                <option value="{{ $value }}" @if ($filters['per_page'] == $value) selected @endif>{{ $value }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary">{{ trans('collocation_candidates.apply') }}</button>
</form>