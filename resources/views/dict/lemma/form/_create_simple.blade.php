        @include('widgets.form.formitem._text', 
                ['name' => 'lemma', 
                 'special_symbol' => true,
                 'title'=>trans('dict.lemma')])
        @include('widgets.form.formitem._select',
                ['name' => 'pos_id',
                 'values' =>$pos_values,
                 'value' =>$pos_id,
                 'title' => trans('dict.pos'),
                 'attributes' => ['id'=>'lemma_pos_id']])
                 
        @include('dict.lemma.form._create_edit_pos_features') 
<div id='new-meanings'>
        @include('dict.lemma._form_create_meaning',
                 ['count' => 0,
                  'title' => '',
                  'langs_for_meaning' => $langs_for_meaning
                 ])
</div>



