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
        
        <?php $attr = ['id'=>'text']; 
              $to_makeup_style = $readonly ? ' style="color: black; font-weight:normal; text-decoration: line-through;"' : '';
        ?>
        @if ($readonly)
            <?php $attr[] = 'readonly'; ?>
            <p class="warning text-has-checked-meaning">
                {{trans('corpus.text_has_checked_meaning')}}
                <button type="button" class="btn btn-info text-unlock">
                    {{trans('corpus.unlock')}}
                </button>

            </p>
        @endif
        
        @include('widgets.form.formitem._textarea', 
                ['name' => 'text', 
                 'special_symbol' => true,
                 'title'=>trans('corpus.text'),
                 'help_text' =>trans('corpus.text_help'),
                 'attributes' => ['id'=>'text', 'readonly'=>$readonly],
                ])
        @if ($action=='edit')
        <div class='to-markup'>
                <input id="to_makeup" name="to_makeup" type="checkbox" value='1'{{$readonly ? ' disabled' : ''}}>
                <label id="to_makeup_label" for="to_makeup" 
                {!!$to_makeup_style!!}>ПЕРЕРАЗМЕТИТЬ</label>
        </div>
        @endif
        
        @if ($action == 'edit')
            @include('widgets.form.formitem._textarea', 
                    ['name' => 'text_structure', 
                     'special_symbol' => true,
                     'title'=>trans('corpus.text_xml'),
                 'attributes' => ['rows'=> $readonly ? 6 : 10]])
        @endif                
{{--        @if ($action == 'edit')
            @include('widgets.form.formitem._textarea', 
                    ['name' => 'text_xml', 
                     'special_symbol' => true,
                     'title'=>trans('corpus.text_xml')])
        @endif --}}
