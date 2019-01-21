    <p id="addWordformSentence"></p>
    @include('widgets.form.formitem._text', 
            ['name' => 'choose-wordform',
             'title' => trans('dict.wordform')]) 
    <p class="link-in-form-field">
        <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}{{$args_by_get}}">{{ trans('messages.create_new_f') }}</a>
    </p>
    @include('widgets.form.formitem._select2',
            ['name' => 'choose-lemma',
             'title' => trans('dict.lemma'),
             'class'=>'select-lemma form-control',
             'multiple' => false
    ]) 

    <div id="addWordformFields"></div>
