    <p>@include('dict.lemma.show.example_sentence', ['relevance'=>'', 'count'=>''])</p>
    <form>
    @include('widgets.form._formitem_select2',
            ['name' => 'choose-lemma',
             'title' => trans('dict.lemma'),
             'class'=>'select-lemma form-control',
             'is_multiple' => false
    ])
</form>