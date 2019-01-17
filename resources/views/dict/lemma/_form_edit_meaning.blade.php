        <h3>@include('widgets.form.formitem._text',
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
                        <td>@include('widgets.form.formitem._text',
                           ['name' => 'ex_meanings['.$meaning->id.'][meaning_text]['.$meaning_lang.']',
                            'special_symbol' => true,
                            'value'=> $meaning_text->meaning_text])</td>
                        <td>
                            @if ($meaning_lang != $lemma->lang_id)
                                @include('widgets.form.formitem._select2',
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
                            $group_class = '';
                        } else {
                            $relation_value =  []; 
                            $group_class = 'empty-relation';
                        }
                    ?>
                    @include('widgets.form.formitem._select2',
                            ['name' => 'ex_meanings['.$meaning->id.'][relation]['.$relation_id.']',
                             'title' => $relation_text,
                             'values' => $all_meanings,
                             'value' => $relation_value,
                             'group_class' => $group_class,
                             'id' => 'relation_'.$meaning->id.'_'.$relation_id,
                             'class'=> 'multiple-select-relation form-control'
                        ])
                @endforeach
                
                <div class="row">
                  <div class="col-xs-3">
                        @include('widgets.form.formitem._select',
                                ['name' => 'new_relation_'.$meaning->id,
                                 'values' => $meaning->missingRelationsList(),
                                 'attributes' => ['id'=>'new_relation_'.$meaning->id]])
                  </div>
                  <div class="col-xs-3">
                      <button type="button" class="btn btn-info add-new-relation" 
                              data-for='{{ $meaning->id}}'>
                          {{trans('dict.add_new_relation')}}
                      </button>
                  </div>
                </div>
