<?php $name_id = $name.'_id'; ?>
        <div id='{{$name}}-field' class="lemma-feature-field">
        @include('widgets.form._formitem_select',
                ['name' => $name_id,
                 'values' => trans('dict.'.$name.'s'),
                 'value' => $obj->$name_id,
                 'title' => trans($lang_file.'.'.$name),
        ])
        </div>
