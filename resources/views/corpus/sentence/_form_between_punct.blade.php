<input type="hidden" id="betweenPunctStep" value="">
<input type="hidden" id="betweenPunctWordNum1" value="">
<input type="hidden" id="betweenPunctWordNum2" value="">

<div class="form-group">
    <label id="betweenPunctLabel">{{ trans('search.punct_between') }}</label>
    <select id="betweenPunctMode" class="form-control">
        <option value="ignore">{{ trans('search.bt_ignore') }}</option>
        <option value="require_any">{{ trans('search.bt_require_any') }}</option>
        <option value="forbid_any">{{ trans('search.bt_forbid_any') }}</option>
    </select>
    <p class="help-block">{{ trans('search.bt_help') }}</p>
</div>

<div class="form-group" id="betweenPunctTypesBlock">
    <label>{{ trans('search.putypes') }}</label><br>
    @foreach ($putype_values as $slug => $name)
        <label class="checkbox-inline">
            <input type="checkbox" class="between-putype" value="{{$slug}}" data-name="{{$name}}">
            {{$name}}
        </label>
    @endforeach
    <p class="help-block">{{ trans('search.bt_types_help') }}</p>
</div>