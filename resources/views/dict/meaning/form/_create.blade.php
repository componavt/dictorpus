@if (isset($new_meaning_n))
<div class='row' style='border-top: 1px #aaa dashed'>
    <div class='col-sm-3'>
        <h3>@include('widgets.form.formitem._text',
                    ['name' => 'new_meanings['.$count.'][meaning_n]',
                    'value'=> $new_meaning_n,
                    'attributes'=>['size' => 2],
                    'tail' => trans('dict.meaning')])</h3>
    </div>
    <div class='col-sm-9'>
        <br>@include('widgets.form.formitem._select2',
                ['name' => 'new_meanings['.$count.'][places]', 
                 'values' => isset($place_values) ? $place_values : [],
                 'class'=>'select-places form-control'])                        
    </div>
</div>
@endif
<table class="table-interpretations">
    <tr>
        <th>{{ trans('dict.lang') }}</th>
        <th>{{ trans('dict.interpretation') }}</th>
        <th></th>
    </tr>
@foreach ($langs_for_meaning as $lang_id => $lang_text)
    @include('dict.meaning.form._lang_meaning_text',
            ['name' => 'new_meanings['.$count.'][meaning_text]['.$lang_id.']'
            ])
@endforeach
</table>
