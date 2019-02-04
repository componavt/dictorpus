@include('widgets.form._url_args_by_post',['url_args'=>$url_args])

<div class="row">
    <div class="col-sm-4">
        @include('widgets.form.formitem._select',
                ['name' => 'lang_id',
                 'values' =>$lang_values,
                 'value' =>$lang_id,
                 'title' => trans('dict.lang'),
                 'attributes' => ['id'=>'lemma_lang_id']])
        @include('widgets.form.formitem._select2',
                ['name' => 'dialect_id', 
                 'values' =>$dialect_values,
                 'value' => $dialect_value,
                 'is_multiple' => false,
                 'title' => trans('dict.dialect_in_lemma_form'),
                 'class'=>'select-dialect form-control'
            ])
        <!--div id='wordforms-field'>
        @include('widgets.form.formitem._text', 
                ['name' => 'wordforms', 
                 'value' => '',
                 'field_comments' => trans('dict.wordforms_field_comments'),
                 'title'=>trans('dict.wordforms')])
        </div-->
    </div>
    <div class="col-sm-4">        
        @include('widgets.form.formitem._select',
                ['name' => 'pos_id',
                 'values' =>$pos_values,
		 'value' => $pos_id,
                 'title' => trans('dict.pos'),
                 'attributes' => ['id'=>'lemma_pos_id']])
                 
        @include('dict.lemma.form._create_edit_pos_features') 
                 
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
    <div class="col-sm-4">        
        @include('widgets.form.formitem._text', 
                ['name' => 'lemma', 
                 'special_symbol' => true,
                 'value' => $lemma_value,
                 'title'=>trans('dict.lemma')])
    </div>
</div>
@if ($action == 'edit')
    @foreach ($lemma->meanings as $meaning)
        @include('dict.lemma._form_edit_meaning')
    @endforeach
    <?php $count=0;?>
@endif

{{-- New meaning --}}
<div id='new-meanings'>
    @if ($action == 'create')
        @include('dict.lemma._form_create_meaning',
                 ['count' => 0,
                  'new_meaning_n' => $new_meaning_n
                 ])
        <?php
            $count = 1;
            $new_meaning_n++;
        ?>
    @endif
</div>

        <button type="button" class="btn btn-info add-new-meaning" 
                 data-count='{{ $count }}' data-meaning_n='{{$new_meaning_n}}'>
            {{trans('dict.add_new_meaning')}}
        </button>

        @include('widgets.form.formitem._submit', ['title' => $submit_title])



