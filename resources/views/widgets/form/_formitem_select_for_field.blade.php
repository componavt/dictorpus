<?php $name_id = $name.'_id'; 
$value = ($obj && $obj->$name_id) ? $obj->$name_id : NULL; ?>
        <div id='{{$name}}-field' class="lemma-feature-field">
        @include('widgets.form._formitem_select',
                ['name' => $name_id,
                 'values' => trans('dict.'.$name.'s'),
                 'value' => $value,
                 'title' => trans($lang_file.'.'.$name),
        ])
        </div>
