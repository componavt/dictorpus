<fieldset class="example-form-section">
    <legend>{{ trans('collocation_candidates.matrix_members') }}</legend>

    <input type="hidden" name="member_ids[]" value="">

    <div id="candidate-matrix-members" class="candidate-member-options">
        @foreach ($all_members as $member)
            <label class="candidate-member-check"
                   title="{{ $member->getMeaningTextByLangCode('ru') }}">
                <input type="checkbox"
                       name="member_ids[]"
                       value="{{ $member->id }}"
                       @if (in_array((int) $member->id, $selected_member_ids, true)) checked @endif>
                {{ $member->lemma->lemma }}<sup>{{ $member->meaning_n }}</sup>
            </label>
        @endforeach
    </div>

    <div class="candidate-member-actions">
        <button id="candidate-members-all" type="button" class="btn btn-default btn-small">
            {{ trans('collocation_candidates.matrix_members_all') }}
        </button>

        <button id="candidate-members-none" type="button" class="btn btn-default btn-small">
            {{ trans('collocation_candidates.matrix_members_none') }}
        </button>
    </div>

    <div class="candidate-matrix-modes">
        <div id="candidate-matrix-row-options" class="form-group" @if ($members->count() < 2) style="display: none;" @endif>
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
        </div>

        <div class="form-group">
            <label for="candidate-matrix-recording">
                {{ trans('collocation_candidates.matrix_recording') }}
            </label>

            <select id="candidate-matrix-recording"
                    name="unrecorded_only"
                    class="form-control">
                <option value="0" @if ($filters['unrecorded_only'] === 0) selected @endif>{{ trans('collocation_candidates.matrix_recording_all') }}</option>
                <option value="1" @if ($filters['unrecorded_only'] === 1) selected @endif>{{ trans('collocation_candidates.matrix_recording_missing') }}</option>
            </select>
        </div>
    </div>
</fieldset>