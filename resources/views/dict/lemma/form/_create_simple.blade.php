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
                 
        @include('dict.lemma.form._create_edit_pos_features', ['is_full_form'=>false]) 
<div id='new-meanings'>
        @include('dict.meaning.form._create',
                 ['count' => 0,
                  'title' => '',
                  'langs_for_meaning' => $langs_for_meaning
                 ])
</div>
        @include('widgets.form.formitem._select',
                ['name' => 'dialect_id', 
                 'values' =>$dialect_values,
                 'value' => $dialect_value,
                 'is_multiple' => false,
                 'title' => trans('dict.dialect_in_lemma_form'),
                 'class'=>'select-dialect form-control'
            ])
        



