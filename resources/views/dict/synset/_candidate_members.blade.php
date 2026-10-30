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

    <div class="form-group" id="candidate-matrix-row-options" @if ($members->count() < 2) style="display: none;" @endif>
        <label for="candidate-matrix-row-mode">
            {{ trans('collocation_candidates.matrix_rows') }}
        </label>

        <select id="candidate-matrix-row-mode" name="row_mode" class="form-control">
            @foreach ([
                0 => 'matrix_rows_all',
                1 => 'matrix_rows_common',
                2 => 'matrix_rows_different',
            ] as $value => $caption)
                <option value="{{ $value }}" @if ($filters['row_mode'] === $value) selected @endif>{{ trans('collocation_candidates.' . $caption) }}</option>
            @endforeach
        </select>

        <p class="help-block">
            {{ trans('collocation_candidates.matrix_rows_hint') }}
        </p>
    </div>
</fieldset>