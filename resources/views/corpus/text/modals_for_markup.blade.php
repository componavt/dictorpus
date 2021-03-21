        @if (User::checkAccess('corpus.edit'))
            @include('widgets.modal',['name'=>'modalAddWordform',
                                  'title'=>trans('corpus.add-wordform'),
                                  'submit_id' => 'save-wordform',
                                  'submit_title' => trans('messages.save'),
                                  'modal_view'=>'dict.wordform._form_create'])
            @include('widgets.modal',['name'=>'modalAddLemma',
                                  'title'=>trans('corpus.add-lemma'),
                                  'submit_id' => 'save-lemma',
                                  'submit_title' => trans('messages.save'),
                                  'modal_view'=>'dict.lemma.form._create_simple'])
            @include('widgets.modal',['name'=>'modalEditWord',
                                  'title'=>trans('corpus.edit_word'),
                                  'submit_id' => 'save-word',
                                  'submit_title' => trans('messages.save'),
                                  'modal_view'=>'corpus.word._form_edit'])
        @endif         
