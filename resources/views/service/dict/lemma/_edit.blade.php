@if($lemma) 
    <span id='lemma-id' data-id='{{ $lemma->id }}'></span>
@endif
<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'lemma', 
                 'special_symbol' => true,
                 'value' => $lemma ? $lemma->stemAffixForm() : null,
                 'title'=>trans('dict.lemma')])
        @include('widgets.form.formitem._select',
                ['name' => 'pos_id',
                 'values' =>$pos_values,
                 'value' =>$lemma ? $lemma->pos_id : null,
                 'title' => trans('dict.pos'),
                 'attributes' => ['id'=>'lemma_pos_id'] ])
    </div>
    <div class="col-sm-6">
        @include('widgets.form.formitem._select',
                ['name' => 'dialect_id', 
                 'values' =>$dialect_values,
                 'value' => $dialect_value ?? 0,
                 'is_multiple' => false,
                 'title' => trans('dict.dialect_in_lemma_form'),
                 'class'=>'select-dialect form-control' ])
                 
        @include('dict.lemma.form._create_edit_pos_features', ['is_full_form'=>false]) 
        
        <div id='phrase-field' class="lemma-feature-field">
        @include('widgets.form.formitem._select2',
                ['name' => 'phrase',
                 'values' => $phrase_values,
                 'value' => array_keys($phrase_values),
                 'title' => trans('dict.phrase_lemmas'),
                 'class'=>'multiple-select-phrase'                            
        ])
        </div>
    </div>
</div>
                 
        
        



