    <p id="addWordformSentence"></p>
    @include('widgets.form._formitem_select2',
            ['name' => 'choose-lemma',
             'title' => trans('dict.lemma'),
             'class'=>'select-lemma form-control',
             'multiple' => false
    ]) 

    <div id="addWordformFields"></div>
