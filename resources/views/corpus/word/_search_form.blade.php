        {!! Form::open(['url' => $url, 'method' => 'get']) !!}
        <div class="search-form row">
            <div class="col-md-3">
                @include('widgets.form.formitem._select', 
                        ['name' => 'search_lang', 
                         'values' => $lang_values,
                         'value' => $url_args['search_lang'],
                         'title' => trans('dict.lang'),
                ])   
            </div>
            <div class="col-md-3">
                @include('widgets.form.formitem._select2',
                    ['name' => 'search_dialect', 
                     'values' =>$dialect_values,
                     'value' => $url_args['search_dialect'],
                     'title' => trans('dict.dialect'),
                     'is_multiple' => false,
                     'class'=>'select-dialect form-control'
                ])                
            </div>
            <div class="col-md-2">
                @include('widgets.form.formitem._text', 
                        ['name' => 'search_word', 
                         'special_symbol' => true,
                         'value' => $url_args['search_word'],
                         'title'=> trans('corpus.word')
                        ])
            </div>
            <div class="col-md-4 search-button-b" style='padding-top:25px'>
                <span>{{trans('search.show_by')}}</span>
                
                @include('widgets.form.formitem._text', 
                        ['name' => 'limit_num', 
                        'value' => $url_args['limit_num'], 
                        'attributes'=>['placeholder' => trans('messages.limit_num')]]) 
                        
                <span style='margin-left: 5px'>{{ trans('messages.records') }}</span>
                @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
            </div>
            @if (user_dict_edit()) 
            <div class="col-md-4 search-linked-field" style="display:flex">
                <label>{{trans('corpus.has_link_with_lemma')}}?&nbsp;&nbsp;</label>
                @include('widgets.form.formitem._radio', 
                        ['name' => 'search_linked', 
                         'values' => trans('corpus.in_dict'),
                         'checked' => $url_args['search_linked']])
            </div>
            @endif
        </div>
        {!! Form::close() !!}
