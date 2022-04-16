        <?php $transtext_lang_id_value = ($action=='edit' && $text->transtext) ? ($text->transtext->lang_id) : 2; ?>
        @include('widgets.form.formitem._select', 
                ['name' => 'transtext.lang_id', 
                 'values' =>$lang_values,
                 'value' => $transtext_lang_id_value,
                 'title' => trans('corpus.transtext_lang') ])
                 
        @include('widgets.form.formitem._select2', 
                ['name' => 'trans_authors', 
                 'values' =>$author_values,
                 'value' => $trans_author_value ?? null,
                 'call_add_onClick' => "addAuthor('trans_authors')",
                 'call_add_title' => trans('messages.create_new_m'),
                 'title' => trans('corpus.trans_author')]) 
                 
        <?php $transtext_title_value = ($action=='edit' && $text->transtext) ? ($text->transtext->title) : NULL; ?>
        @include('widgets.form.formitem._text', 
                ['name' => 'transtext.title', 
                 'special_symbol' => true,
                 'value' => $transtext_title_value,
                 'title'=>trans('corpus.transtext_title')])
                 
        <?php $transtext_text_value = ($action=='edit' && $text->transtext) ? ($text->transtext->text) : NULL; ?>
        @include('widgets.form.formitem._textarea', 
                ['name' => 'transtext.text', 
                 'help_text' =>trans('corpus.text_help')
                    ."<div class=\"buttons-div\"><input class=\"special-symbol-b special-symbol-sup\" title=\""
                    .trans('messages.supper_text')."\" type=\"button\" value=\"5\" onclick=\"toSup('transtext_text')\"></div>",
                 'special_symbol' => true,
                 'value' => $transtext_text_value,
                 'title'=>trans('corpus.transtext_text'),
                 'attributes' => ['rows'=>$action=='edit' ? 25 : 10]])
                 
