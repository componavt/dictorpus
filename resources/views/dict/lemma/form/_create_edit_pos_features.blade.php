@if ($is_full_form)
        @include('widgets.form.formitem._radio_for_field', 
                ['name' => 'animacy', 
                 'title'=>trans('dict.animacy')])
        @include('widgets.form.formitem._checkbox_for_field', 
                ['name' => 'abbr', 
                 'title'=>trans('dict.abbr')])
@endif   

@include('widgets.form.formitem._checkbox_for_field', 
        ['name' => 'without_gram', 
         'value' => $lemma && $lemma->features ? $lemma->features->without_gram : null,
         'title'=>trans('dict.without_gram')])
         
@include('widgets.form.formitem._checkbox_for_field', 
        ['name' => 'reflexive', 
         'value' => $lemma && $lemma->features ? $lemma->features->reflexive : null,
         'title'=>trans('dict.reflexive').' '.trans('dict.verb')])
@include('widgets.form.formitem._checkbox_for_field', 
        ['name' => 'impersonal', 
         'value' => $lemma && $lemma->features ? $lemma->features->impersonal : null,
         'title'=>trans('dict.impersonal').' '.trans('dict.verb')])
@if ($is_full_form)                 
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
                 
        @include('widgets.form.formitem._select_for_field', 
                ['name' => 'comptype', 
                 'lang_file'=> 'dict'])
@endif                 

@include('widgets.form.formitem._select_for_field', 
        ['name' => 'number', 
         'value' => $lemma && $lemma->features ? $lemma->features->number : null,
         'without_id' => true,
         'lang_file'=> 'dict'])
                 
