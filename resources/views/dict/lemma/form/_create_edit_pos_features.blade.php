        @include('widgets.form.formitem._radio_for_field', 
                ['name' => 'animacy', 
                 'title'=>trans('dict.animacy')])
        @include('widgets.form.formitem._checkbox_for_field', 
                ['name' => 'abbr', 
                 'title'=>trans('dict.abbr')])
        @include('widgets.form.formitem._select_for_field', 
                ['name' => 'number', 
                 'without_id' => true,
                 'lang_file'=> 'dict'])
                 
        @include('widgets.form.formitem._checkbox_for_field', 
                ['name' => 'reflexive', 
                 'title'=>trans('dict.reflexive').' '.trans('dict.verb')])
        @include('widgets.form.formitem._radio_for_field', 
                ['name' => 'transitive', 
                 'title'=>trans('dict.transitive').' '.trans('dict.verb')])

        @include('widgets.form.formitem._select_for_field', 
                ['name' => 'prontype', 
                 'lang_file'=> 'dict'])
        @include('widgets.form.formitem._select_for_field', 
                ['name' => 'numtype', 
                 'lang_file'=> 'dict'])
        @include('widgets.form.formitem._select_for_field', 
                ['name' => 'advtype', 
                 'lang_file'=> 'dict'])
        @include('widgets.form.formitem._select_for_field', 
                ['name' => 'degree', 
                 'lang_file'=> 'dict'])
