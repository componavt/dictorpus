@php
    $reference_type_values = trans('corpus.bible_reference_types');
@endphp

<div class="row js-bible-field-row">
    <div class="col-sm-3">
        <label for="bible-reference-type-{{ $index }}">
            {{ trans('corpus.bible_reference_type') }}
        </label>

        <select
            id="bible-reference-type-{{ $index }}"
            class="form-control"
            name="bibles[{{ $index }}][reference_type]"
        >
            @foreach (trans('corpus.bible_reference_types') as $value => $label)
                <option
                    value="{{ $value }}"
                    {{ (int) $bible_row['reference_type'] === (int) $value ? 'selected' : '' }}
                >
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label>{{ trans('corpus.book') }}</label>

            <select
                name="bibles[{{ $index }}][bible_id]"
                class="multiple-select-bible form-control"
            >
                <option value=""></option>

                @foreach ($bible_values as $bible_id => $bible_name)
                    <option
                        value="{{ $bible_id }}"
                        @if (
                            (string) $bible_id ===
                            (string) ($bible_row['bible_id'] ?? '')
                        )
                            selected
                        @endif
                    >
                        {{ $bible_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group">
            <label>{{ trans('corpus.chapter') }}</label>

            <input
                type="text"
                name="bibles[{{ $index }}][chapter]"
                value="{{ $bible_row['chapter'] ?? '' }}"
                class="form-control"
            >
        </div>
    </div>

    <div class="col-sm-2">
        <p style="margin-bottom: 5px">
            <b>{{ trans('corpus.verses') }}</b>
        </p>

        <div style="display: flex">
            <span style="margin-right: 10px">
                {{ trans('messages.from') }}
            </span>

            <input
                type="text"
                name="bibles[{{ $index }}][verse_from]"
                value="{{ $bible_row['verse_from'] ?? '' }}"
                class="form-control"
            >

            <span style="margin: 0 10px">
                {{ trans('corpus.to') }}
            </span>

            <input
                type="text"
                name="bibles[{{ $index }}][verse_to]"
                value="{{ $bible_row['verse_to'] ?? '' }}"
                class="form-control"
            >
        </div>
    </div>

    <div class="col-sm-1 text-center" style="padding-top: 25px">
        <button
            type="button"
            class="remove-bible-field btn btn-link"
            title="{{ trans('messages.delete') }}"
            aria-label="{{ trans('messages.delete') }}"
        >
            <i class="fa fa-times"></i>
        </button>
    </div>
</div>