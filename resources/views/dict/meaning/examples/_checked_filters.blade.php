<input type="hidden" name="source_only_checked" value="0">
<div class="checkbox">
    <label>
        <input type="checkbox" name="source_only_checked" value="1" @if ($source_only_checked) checked @endif>
        {{ trans('meaning_examples.source_only_checked') }}
    </label>
</div>

<input type="hidden" name="only_checked" value="0">
<div class="checkbox">
    <label>
        <input type="checkbox" name="only_checked" value="1" @if ($only_checked) checked @endif>
        {{ trans('meaning_examples.partner_only_checked') }}
    </label>
</div>