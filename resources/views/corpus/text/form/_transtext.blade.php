        @include('widgets.form.formitem._select', 
                ['name' => 'transtext.lang_id', 
                 'values' =>$lang_values,
                 'value' => $action=='edit' && $text->transtext ? $text->transtext->lang_id : 2,
                 'title' => trans('corpus.transtext_lang') ])
                 
        @include('widgets.form.formitem._select2', 
                ['name' => 'trans_authors', 
                 'values' =>$author_values,
                 'value' => $trans_author_value ?? null,
                 'call_add_onClick' => "addAuthor('trans_authors')",
                 'call_add_title' => trans('messages.create_new_m'),
                 'title' => trans('corpus.trans_author')]) 
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'transtext.title', 
                 'special_symbol' => true,
                 'value' => $action=='edit' && $text->transtext ? $text->transtext->title : NULL,
                 'title'=>trans('corpus.transtext_title')])
                 
        @include('widgets.form.formitem._textarea', 
                ['name' => 'transtext.text', 
                 'help_text' =>trans('corpus.text_help')
                    ."<div class=\"buttons-div\"><input class=\"special-symbol-b special-symbol-sup\" title=\""
                    .trans('messages.supper_text')."\" type=\"button\" value=\"5\" onclick=\"toSup('transtext_text')\"></div>",
                 'special_symbol' => true,
                 'value' => $action=='edit' && $text->transtext ? $text->transtext->text : NULL,
                 'title'=>trans('corpus.transtext_text'),
                 'attributes' => ['rows'=>$action=='edit' ? 24 : 10]])
                 
