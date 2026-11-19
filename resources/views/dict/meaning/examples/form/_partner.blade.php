<style>
    #example-partner-gramsets-group .select2-selection__choice {
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        padding: 2px 6px;
        line-height: 1.4;
    }

    #example-partner-gramsets-group .select2-selection__choice__remove {
        display: inline-block;
        position: static;
        float: none;
        width: auto;
        height: auto;
        margin: 0 6px 0 0;
        padding: 0;
        border: 0;
        line-height: inherit;
    }

    #example-partner-gramsets-group .select2-selection__choice__display {
        display: inline;
        padding: 0;
        white-space: nowrap;
    }
</style>

@if ($candidate_mode)
    @include('dict.meaning.candidates._pos_filter')
@else
    @include('dict.meaning.candidates._pos_filter', [
        'pos_values' => \App\Models\Dict\PartOfSpeech::getGroupedList(),
        'filters' => [
            'partner_pos_id' => (int) request()->input('partner_pos_id', 0),
        ],
    ])

    <div id="example-partner-gramsets-group" class="form-group"
        @if (empty($partner_pos_id) || !count($partner_gramsets ?? [])) style="display: none;" @endif>
        <label for="example-search-partner-gramsets">
            {{ trans('meaning_examples.partner_gramsets') }}
        </label>

        <select id="example-search-partner-gramsets"
                name="partner_gramset_ids[]"
                class="form-control example-extra-select"
                multiple
                data-search-url="{{ LaravelLocalization::localizeURL('/dict/meaning/'.$source->id.'/examples/partner-gramsets') }}"
                data-placeholder="{{ trans('meaning_examples.partner_gramsets_placeholder') }}"
                data-loading="{{ trans('meaning_examples.partner_gramsets_loading') }}"
                data-load-error="{{ trans('meaning_examples.partner_gramsets_load_error') }}"
                style="width: 100%;"
                @if (empty($partner_pos_id) || !count($partner_gramsets ?? []))
                    disabled
                @endif>
            @foreach (($partner_gramsets ?? []) as $id => $text)
                <option value="{{ $id }}" @if (in_array((int) $id, $selected_filters['partner_gramset_ids'] ?? [], true)) selected @endif>
                    {{ $text }}
                </option>
            @endforeach
        </select>

        <p class="help-block example-partner-gramsets-status"
        role="status"
        aria-live="polite"></p>
    </div>

    <div class="form-group">
        <label for="example-search-partner">{{ trans('meaning_examples.partner') }}</label>
        <select id="example-search-partner" name="partner_meaning_id" class="form-control"
                data-search-url="{{ LaravelLocalization::localizeURL('/dict/meaning/'.$source->id.'/examples/partners') }}"
                data-placeholder="{{ trans('meaning_examples.partner_placeholder') }}"
                style="width: 100%;">
            <option value=""></option>
            @if ($partner)
                <option value="{{ $partner->id }}" data-pos-id="{{ (int) $partner->lemma->pos_id }}" selected>{{ $partner_text }}</option>
            @endif
        </select>
        <p class="help-block">{{ trans('meaning_examples.partner_hint') }}</p>
    </div>
@endif

@include('dict.meaning.examples.form._checkbox', [
    'field' => 'only_checked',
    'caption' => 'partner_only_checked',
])