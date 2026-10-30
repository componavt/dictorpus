<fieldset class="example-form-section">
    <legend>{{ trans('collocation_candidates.matrix_members') }}</legend>

    <input type="hidden" name="member_ids[]" value="">

    <select id="candidate-matrix-members" name="member_ids[]" multiple
            class="form-control example-extra-select"
            aria-label="{{ trans('collocation_candidates.matrix_members') }}"
            data-placeholder="{{ trans('collocation_candidates.matrix_members_placeholder') }}">
        @foreach ($all_members as $member)
            <option value="{{ $member->id }}" @if (in_array((int) $member->id, $selected_member_ids, true)) selected @endif>{{ $member->lemma->lemma }} [{{ $member->meaning_n }}] — {{ $member->getMeaningTextByLangCode('ru') }}</option>
        @endforeach
    </select>

    <p class="help-block">
        {{ trans('collocation_candidates.matrix_members_hint') }}
    </p>
</fieldset>