<?php 
$name_id = (isset($without_id) && $without_id) ? $name : $name.'_id'; 
$value = (isset($obj) && $obj->$name_id) ? $obj->$name_id : NULL; 
?>
        <div id='{{$name}}-field' class="lemma-feature-field">
        @include('widgets.form.formitem._select',
                ['name' => $name_id,
                 'value' => $value,
                 'values' => $values ?? trans('dict.'.$name.'s'),
                 'title' => trans($lang_file.'.'.$name),
        ])
        </div>
