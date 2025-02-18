        @include('widgets.form.formitem._select', 
                ['name' => 'lang_id', 
                 'values' =>$lang_values,
                 'title' => trans('dict.lang'),
                 'attributes' => ['id'=>'lang_id']])
                 
        @include('widgets.form.formitem._select2', 
                ['name' => 'authors', 
                 'values' =>$author_values,
                 'value' => $author_value ?? null,
                 'call_add_onClick' => "addAuthor('authors')",
                 'call_add_title' => trans('messages.create_new_m'),
                 'title' => trans('corpus.author')]) 
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'title', 
                 'title'=>trans('corpus.title')])
        
        @if ($readonly)
            <p class="warning text-has-checked-meaning">
                @if (!$text->hasImportantExamples())
                {{trans('corpus.text_has_checked_meaning')}}
                    <button type="button" class="btn btn-info text-unlock">
                        {{trans('corpus.unlock')}}
                    </button>
                @else
                {{trans('corpus.text_has_best_checked_meaning')}}                
                @endif
            </p>
        @endif
        
        @include('widgets.form.formitem._textarea', 
                ['name' => 'text', 
                 'special_symbol' => true,
                 'title'=>trans('corpus.text'),
                 'help_text' =>trans('corpus.text_help')
                    ."<div class=\"buttons-div\"><input class=\"special-symbol-b special-symbol-sup\" title=\""
                    .trans('messages.supper_text')."\" type=\"button\" value=\"5\" onclick=\"toSup('text')\"></div>",
                 'attributes' => ['id'=>'text', 'readonly'=>$readonly, 
                                  'rows'=> 10],
                ])
        @if ($action=='edit')
            @php 
                  $to_makeup_style = $readonly ? ' style="color: black; font-weight:normal; text-decoration: line-through;"' : '';
            @endphp
            <div class='to-markup'>
                    <input id="to_makeup" name="to_makeup" type="checkbox" value='1'{{$readonly ? ' disabled' : ''}} 
                           @if (empty($text->text_xml)) checked @endif>
                    <label id="to_makeup_label" for="to_makeup" 
                    {!!$to_makeup_style!!}>ПЕРЕРАЗМЕТИТЬ</label>
            </div>

                @include('widgets.form.formitem._textarea', 
                        ['name' => 'text_structure', 
                         'special_symbol' => true,
                         'title'=>trans('corpus.text_xml'),
                     'attributes' => ['readonly'=>$readonly,
                                      'rows'=> $readonly ? 5 : 10]])
    {{--
                @include('widgets.form.formitem._textarea', 
                        ['name' => 'text_xml', 
                         'special_symbol' => true,
                         'title'=>trans('corpus.text_xml')])
            --}}
        @endif
