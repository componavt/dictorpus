<div class="form-group">
    <label for="candidate-partner-pos">
        {{ trans('collocation_candidates.partner_pos') }}
    </label>

    <select id="candidate-partner-pos" name="partner_pos_id" class="form-control candidate-pos">
        <option value="0">{{ trans('collocation_candidates.all_pos') }}</option>

        @foreach ($pos_values as $category => $positions)
            <optgroup label="{{ $category }}">
                @foreach ($positions as $id => $name)
                    <option value="{{ $id }}" @if ($filters['partner_pos_id'] == $id) selected @endif>
                        {{ $name }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
</div>