<div class="row">
    <div class="col-sm-4">
        @include('widgets.form._formitem_text', ['name' => 'lemma', 'title'=>trans('dict.lemma')])
    </div>
    <div class="col-sm-4">        
        @include('widgets.form._formitem_select',
                ['name' => 'lang_id',
                 'values' =>$lang_values,
                 'title' => trans('dict.lang'),
                 'attributes' => ['id'=>'lemma_lang_id']])
    </div>
    <div class="col-sm-4">        
        @include('widgets.form._formitem_select',
                ['name' => 'pos_id',
                 'values' =>$pos_values,
                 'title' => trans('dict.pos'),
                 'attributes' => ['id'=>'lemma_pos_id']])
    </div> 
</div>
@if ($action == 'edit')
    @foreach ($lemma->meanings as $meaning)
        <h3>@include('widgets.form._formitem_text',
                   ['name' => 'ex_meanings['.$meaning->id.'][meaning_n]',
                    'value'=> $meaning->meaning_n,
                    'attributes'=>['size' => 2],
                    'tail' => trans('dict.meaning')])</h3>
                <table class="table-interpretations-translations">
                    <tr>
                        <th>{{ trans('dict.lang') }}</th>
                        <th>{{ trans('dict.interpretation') }}</th>
                        <th>{{ trans('dict.translation') }}</th>
                    </tr>
                @foreach ($meaning->meaningTextsWithAllLangs() as $meaning_lang => $meaning_text)
                    <tr>
                        <td>{{ $meaning_text->lang_name}}</td>
                        <td>@include('widgets.form._formitem_text',
                           ['name' => 'ex_meanings['.$meaning->id.'][meaning_text]['.$meaning_lang.']',
                            'value'=> $meaning_text->meaning_text])</td>
                        <td>
                            @if ($meaning_lang != $lemma->lang_id)
                                @include('widgets.form._formitem_select2',
                                        ['name' => 'ex_meanings['.$meaning->id.'][translation]['.$meaning_lang.']',
                                         'values' => $translation_values[$meaning->id][$meaning_lang],
                                         'value' => array_keys($translation_values[$meaning->id][$meaning_lang]),
                                         'class'=>'multiple-select-translation-'.$meaning_lang                            
                                ])
                            @endif
                        </td>
                    </tr>
                @endforeach
                </table>

                @foreach ($relation_values as $relation_id => $relation_text)
                    <?php 
                        if (isset($relation_meanings[$meaning->id][$relation_id])) {
                            $relation_value =  $relation_meanings[$meaning->id][$relation_id]; 
                            $style = 'display: block';
                        } else {
                            $relation_value =  []; 
                            $style = 'display: none';
                        }
                    ?>
                    @include('widgets.form._formitem_select2',
                            ['name' => 'ex_meanings['.$meaning->id.'][relation]['.$relation_id.']',
                             'title' => $relation_text,
                             'values' => $all_meanings,
                             'value' => $relation_value,
                             'style' => $style,
                             'id' => 'relation_'.$meaning->id.'_'.$relation_id,
                             'class'=>'multiple-select-relation form-control'
                        ])
                @endforeach
                
                <div id="new-relations">
                </div>
{{--                @include('dict.lemma._form_new_relations')--}}
                <div class="row">
                  <div class="col-xs-3">
                        @include('widgets.form._formitem_select',
                                ['name' => 'new_relation',
                                 'values' => $meaning->missingRelationsList(),
                                 'attributes' => ['id'=>'new_relation_id']])
                  </div>
                  <div class="col-xs-3">
                      <button type="button" class="btn btn-info add-new-relation" 
                              data-for='{{ $meaning->id}}'>
                          {{trans('dict.add_new_relation')}}
                      </button>
                  </div>
                </div>
            @endforeach
        @endif

        {{-- New meaning --}}
        <div>
            <h3>@include('widgets.form._formitem_text',
                       ['name' => 'new_meanings[0][meaning_n]',
                        'value'=> $new_meaning_n,
                        'attributes'=>['size' => 2],
                        'tail' => trans('dict.meaning')])</h3>
            <table class="table-interpretations-translations">
                <tr>
                    <th>{{ trans('dict.lang') }}</th>
                    <th>{{ trans('dict.interpretation') }}</th>
                    <th></th>
                </tr>
            @foreach ($langs_for_meaning as $lang_id => $lang_text)
                <tr>
                    <td>{{ $lang_text }}&nbsp; </td>
                    <td>@include('widgets.form._formitem_text',
                       ['name' => 'new_meanings[0][meaning_text]['.$lang_id.']'])</td>
                    <td></td>
                </tr>
            @endforeach
            </table>
        </div>

        @include('widgets.form._formitem_btn_submit', ['title' => $submit_title])
{{--
        <p><b>{{ trans('dict.wordforms') }}</b></p>
        @if ($action == 'edit')
            @include('dict.lemma._form_edit_wordforms')
        @endif
--}}


