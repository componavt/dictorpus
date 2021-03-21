    <p id="addWordformSentence"></p>
    <a id="call-edit-word" class="btn btn-success">
        {{trans('corpus.edit_word')}}
    </a>
    
    @include('widgets.form.formitem._text', 
            ['name' => 'choose-wordform',
             'special_symbol' => true,
             'title' => trans('dict.wordform')]) 
    <p class="link-in-form-field">
        <a id="call-add-lemma">{{ trans('messages.create_new_f') }}</a> {{-- href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}" --}}
    </p>
    @include('widgets.form.formitem._select2',
            ['name' => 'choose-lemma',
             'title' => trans('dict.lemma'),
             'class'=>'select-lemma form-control',
             'multiple' => false
    ]) 

    <div id="addWordformFields"></div>
        
    <div id="prediction-block">
        <div class="waiting blink">{{trans('corpus.wait_while_search')}}</div>
        <div id="prediction-content"></div>
    </div>

    @include('widgets.form.formitem._checkbox_group', 
            ['name' => 'choose-dialect',
             'checked' => $dialect_value,
             'values' => $dialect_values,
             'title' => trans('dict.dialects')]) 
    