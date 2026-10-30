<input type="hidden" name="{{ $field }}" value="0">
<div class="checkbox">
    <label>
        <input type="checkbox" name="{{ $field }}" value="1" @if ($form_values[$field]) checked @endif>
        {{ trans('meaning_examples.'.$caption) }}
    </label>
</div>