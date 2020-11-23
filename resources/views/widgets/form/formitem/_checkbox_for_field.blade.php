        <?php $checked = (isset($obj->$name) && $obj->$name==1 ? 'checked' : NULL); ?>
        <div id='{{$name}}-field' class="lemma-feature-field">
        @include('widgets.form.formitem._checkbox', 
                ['name' => $name, 
                 'value' => 1,
                 'checked' => $checked,
                 'title'=>$title ?? null])
        </div>
