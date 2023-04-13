        @include('widgets.form.formitem._text', 
                ['name' => 'new_lemma', 
                 'special_symbol' => true,
                 'value' => $lemma->lemma,
                 'title'=>trans('dict.lemma')])
