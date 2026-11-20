<div class="row" style="margin-bottom: 20px;">
    <div class="col-sm-6">
        <p><b>Язык:</b> {{ isset($synset) ? $synset->lang->name : $lang->name }}</p>

        <div class="form-group {{ $errors->has('descr') ? 'has-error' : '' }}">
            <label for="descr">{{ trans('dict.synset_general_meaning') }}</label>
            {!! Form::text('descr', old('descr', $synset->descr ?? ''), ['id' => 'descr', 'class' => 'form-control']) !!}
            @if ($errors->has('descr'))
                <p class="help-block">{{ $errors->first('descr') }}</p>
            @endif
        </div>
    </div>
    <div class="col-sm-6">
        @include('widgets.form.formitem._select', [
            'name' => 'pos_id',
            'title' => 'часть&nbsp;речи:&nbsp;',
            'is_flex' => true,
            'value' => $default_pos_id,
            'values' => $pos_values,
        ])

        <p class="help-block">{{ trans('synset_form.pos_change_hint') }}</p>
    </div>
</div>