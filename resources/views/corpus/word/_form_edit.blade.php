    <p id="editWordSentence"></p>
    
    @include('widgets.form.formitem._text', 
            ['name' => 'word',
             'special_symbol' => true,
             'title' => trans('corpus.word')]) 
    