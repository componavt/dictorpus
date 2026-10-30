@if ($candidate_mode)
    @include('dict.meaning.candidates._pos_filter')
@else
    <div class="form-group">
        <label for="example-search-partner">{{ trans('meaning_examples.partner') }}</label>
        <select id="example-search-partner" name="partner_meaning_id" class="form-control"
                data-search-url="{{ LaravelLocalization::localizeURL('/dict/meaning/'.$source->id.'/examples/partners') }}"
                data-placeholder="{{ trans('meaning_examples.partner_placeholder') }}"
                style="width: 100%;">
            <option value=""></option>
            @if ($partner)
                <option value="{{ $partner->id }}" selected>{{ $partner_text }}</option>
            @endif
        </select>
        <p class="help-block">{{ trans('meaning_examples.partner_hint') }}</p>
    </div>
@endif

@include('dict.meaning.examples.form._checkbox', [
    'field' => 'only_checked',
    'caption' => 'partner_only_checked',
])