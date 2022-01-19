<?php
    if (!isset($radio_value)) {
        $radio_value = trans('messages.bin_answers');
    }
?>

        <?php //$checked = (isset($obj->$name) ? $obj->$name : NULL); ?>

        <div id='{{$name}}-field' class="lemma-feature-field">
        @include('widgets.form.formitem._radio', 
                ['name' => $name, 
                 'values' => $radio_value,
                 'checked' => $obj->$name ?? NULL,
                 'title'=>$title ?? ''])
        </div>
