        @include('widgets.form.formitem._text', 
                ['name' => 'cyrtext.title', 
                 'special_symbol' => true,
                 'value' => $action=='edit' && $text->cyrtext ? $text->cyrtext->title : NULL,
                 'title'=>trans('corpus.cyrtext_title')])
                 
        @include('widgets.form.formitem._textarea', 
                ['name' => 'cyrtext.text', 
                 'special_symbol' => true,
                 'value' => $action=='edit' && $text->cyrtext ? $text->cyrtext->text : NULL,
                 'title'=>trans('corpus.cyrtext_text'),
                 'attributes' => ['rows'=>10]])
