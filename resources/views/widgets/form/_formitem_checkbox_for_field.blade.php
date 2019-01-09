        <?php $checked = (isset($obj->$name) && $obj->$name==1 ? 'checked' : NULL); ?>
        <div id='{{$name}}-field'>
        @include('widgets.form._formitem_checkbox', 
                ['name' => $name, 
                 'value' => 1,
                 'checked' => $checked,
                 'title'=>$title])
        </div>
