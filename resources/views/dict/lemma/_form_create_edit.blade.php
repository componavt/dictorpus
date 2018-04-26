@include('widgets.form._url_args_by_post',['url_args'=>$url_args])
        
<div class="row">
    <div class="col-sm-4">
        @include('widgets.form._formitem_text', 
                ['name' => 'lemma', 
                 'special_symbol' => true,
                 'title'=>trans('dict.lemma')])
    </div>
    <div class="col-sm-4">        
        @include('widgets.form._formitem_select',
                ['name' => 'pos_id',
                 'values' =>$pos_values,
                 'title' => trans('dict.pos'),
                 'attributes' => ['id'=>'lemma_pos_id']])
                 
            <?php $checked = (isset($lemma->reflexive) && $lemma->reflexive==1 ? 'checked' : NULL); ?>
        <div id='reflexive-field'>
        @include('widgets.form._formitem_checkbox', 
                ['name' => 'reflexive', 
                 'value' => 1,
                 'checked' => $checked,
                 'tail'=>trans('dict.reflexive_verb').' '.trans('dict.verb')])
        </div>
    </div> 
    <div class="col-sm-4">        
        @include('widgets.form._formitem_select',
                ['name' => 'lang_id',
                 'values' =>$lang_values,
                 'value' =>$lang_id,
                 'title' => trans('dict.lang'),
                 'attributes' => ['id'=>'lemma_lang_id']])
        <div id='wordforms-field'>
        @include('widgets.form._formitem_text', 
                ['name' => 'wordforms', 
                 'value' => '',
                 'field_comments' => trans('dict.wordforms_field_comments'),
                 'title'=>trans('dict.wordforms')])
        </div>
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

        @include('widgets.form._formitem_btn_submit', ['title' => $submit_title])



