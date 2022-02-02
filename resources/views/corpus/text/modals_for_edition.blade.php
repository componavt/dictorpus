        @include('widgets.modal',['name'=>'modalAddAuthor',
                              'title'=>trans('corpus.add_author'),
                              'submit_onClick' => 'saveAuthor()',
                              'author' => null,
                              'submit_title' => trans('messages.save'),
                              'modal_view'=>'corpus.author._form_create_edit'])
        
        @include('widgets.modal',['name'=>'modalAddInformant',
                              'title'=>trans('corpus.add_informant'),
                              'submit_onClick' => 'saveInformant()',
                              'submit_title' => trans('messages.save'),
                              'modal_view'=>'corpus.informant._form_create_edit'])
        
        @include('widgets.modal',['name'=>'modalAddDistrict',
                              'title'=>trans('corpus.add_district'),
                              'submit_onClick' => 'saveDistrict()',
                              'submit_title' => trans('messages.save'),
                              'modal_view'=>'corpus.district._form_create_edit'])
        
        @include('widgets.modal',['name'=>'modalAddPlace',
                              'title'=>trans('corpus.add_place'),
                              'submit_onClick' => 'savePlace()',
                              'submit_title' => trans('messages.save'),
                              'modal_view'=>'corpus.place._form_create_simple'])
        
        @include('widgets.modal',['name'=>'modalAddRecorder',
                              'title'=>trans('corpus.add_recorder'),
                              'submit_onClick' => 'saveRecorder()',
                              'submit_title' => trans('messages.save'),
                              'modal_view'=>'corpus.recorder._form_create_edit'])

        @include('widgets.modal',['name'=>'modalAddTopic',
                              'title'=>trans('corpus.add_topic'),
                              'submit_onClick' => 'saveTopic()',
                              'submit_title' => trans('messages.save'),
                              'modal_view'=>'corpus.topic._form_create_edit'])
                              