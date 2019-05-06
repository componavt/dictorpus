                <tr>
                    <td>{{ $lang_text }}&nbsp; </td>
                    <td>
                        @include('widgets.form.formitem._text',
                                ['name' => $name,
                                 'special_symbol' => true,
                                 'value' => isset($meaning_value) ? $meaning_value : ''
                                ])
                    </td>
                    <td>
                        @if (isset($is_translated) && $is_translated)
                            @include('widgets.form.formitem._select2',
                                    ['name' => 'ex_meanings['.$meaning->id.'][translation]['.$meaning_lang.']',
                                     'values' => $translation_values[$meaning->id][$meaning_lang],
                                     'value' => array_keys($translation_values[$meaning->id][$meaning_lang]),
                                     'class'=>'multiple-select-translation-'.$meaning_lang                            
                            ])
                        @endif
                    </td>
                </tr>
