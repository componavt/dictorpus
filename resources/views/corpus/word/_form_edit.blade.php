    <p id="editWordSentence"></p>
    
    @include('widgets.form.formitem._text', 
            ['name' => 'word',
             'special_symbol' => true,
             'title' => trans('corpus.word')]) 

@if (!empty($text) && !empty($text->cyrtext))        
    @include('widgets.form.formitem._text', 
            ['name' => 'cyr_word',
             'special_symbol' => true,
             'title' => trans('corpus.word'). ' на кириллице']) 
@endif             