        @include('widgets.form.formitem._text', 
                ['name' => 'lemma', 
                 'special_symbol' => true,
                 'title'=>trans('dict.lemma')])
        @include('widgets.form.formitem._select',
                ['name' => 'pos_id',
                 'values' =>$pos_values,
                 'value' =>$pos_id ?? null,
                 'title' => trans('dict.pos'),
                 'attributes' => ['id'=>'lemma_pos_id']])
                 
        @include('dict.lemma.form._create_edit_pos_features', ['is_full_form'=>false]) 
        
        @for ($i=0; $i<$total_meanings; $i++)
        <div id='new-meanings'>
                @include('dict.meaning.form._create',
                         ['count' => $i,
                          'langs_for_meaning' => $langs_for_meaning])
        </div>
        @endfor
        
        @include('widgets.form.formitem._select',
                ['name' => 'dialect_id', 
                 'values' =>$dialect_values,
                 'value' => $dialect_value ?? 0,
                 'is_multiple' => false,
                 'title' => trans('dict.dialect_in_lemma_form'),
                 'class'=>'select-dialect form-control'
            ])
        



