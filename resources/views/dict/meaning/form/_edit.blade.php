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
                    @include('dict.meaning.form._lang_meaning_text',
                            ['lang_text' => $meaning_text->lang_name,
                             'name' => 'ex_meanings['.$meaning->id.'][meaning_text]['.$meaning_lang.']',
                             'meaning_value' => $meaning_text->meaning_text,
                             'is_translated' => $meaning_lang != $lemma->lang_id
                            ])
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
                
            @include('dict.meaning.form._add_new_relation')
