    <p id="addWordformSentence"></p>
    @include('widgets.form._formitem_select2',
            ['name' => 'choose-lemma2',
             'title' => trans('dict.lemma_meaning'),
             'class'=>'select-lemma2 form-control'
    ]) 

    <p class='with-first-big-letter'><b>{{trans('dict.pos')}}:</b> <span id="addWordformPOS"></span></p>

    @include('widgets.form._formitem_select', 
            ['name' => 'choose-gramset',
             'title' => trans('dict.gramsets')]) 

    @include('widgets.form._formitem_select', 
            ['name' => 'choose-dialect',
             'title' => trans('dict.dialect')]) 
                 
