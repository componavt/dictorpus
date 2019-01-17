                    @include('widgets.form.formitem._select2',
                            ['name' => 'ex_meanings['.$meaning_id.'][relation]['.$relation_id.']',
                             'title' => $relation_text,
                             'class'=>'multiple-select-relation form-control'
                        ])
