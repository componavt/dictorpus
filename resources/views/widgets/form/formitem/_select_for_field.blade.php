<?php $name_id = $name.'_id'; 
$value = (isset($obj) && $obj->$name_id) ? $obj->$name_id : NULL; 
$values = isset($values) ? $values : trans('dict.'.$name.'s'); ?>
        <div id='{{$name}}-field' class="lemma-feature-field">
        @include('widgets.form.formitem._select',
                ['name' => $name_id,
                 'value' => $value,
                 'values' => $values,
                 'title' => trans($lang_file.'.'.$name),
        ])
        </div>
