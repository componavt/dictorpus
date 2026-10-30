<div class="example-combination">
    <div class="example-distance-fields">
        <div class="example-distance-range">
            <label for="example-search-distance-from">{{ trans('meaning_examples.distance_from') }}</label>
            <input id="example-search-distance-from" type="number" step="1" name="distance_from"
                   class="form-control" value="{{ $form_values['distance_from'] }}"
                   @if ($form_values['whole_sentence']) disabled @endif required>

            <label for="example-search-distance-to">{{ trans('meaning_examples.distance_to_short') }}</label>
            <input id="example-search-distance-to" type="number" step="1" name="distance_to"
                   class="form-control" value="{{ $form_values['distance_to'] }}"
                   @if ($form_values['whole_sentence']) disabled @endif required>
        </div>

        @foreach (['distance_from', 'distance_to'] as $field)
            <input type="hidden" name="{{ $field }}" class="example-distance-copy"
                   value="{{ $form_values[$field] }}"
                   @if (!$form_values['whole_sentence']) disabled @endif>
        @endforeach
    </div>

    <div>
        @include('dict.meaning.examples.form._checkbox', [
            'field' => 'whole_sentence',
            'caption' => 'whole_sentence',
        ])
    </div>

    <div id="example-search-partner-options">
        @include('dict.meaning.examples.form._checkbox', [
            'field' => 'same_gramset',
            'caption' => 'same_gramset',
        ])
    </div>
</div>