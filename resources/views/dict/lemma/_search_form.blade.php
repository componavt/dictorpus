        {!! Form::open(['url' => $url,
                             'method' => 'get',
                             'class' => 'form-inline'])
        !!}
        @if ($is_search_id)
            @include('widgets.form._formitem_text',
                    ['name' => 'search_id',
                    'value' => $url_args['search_id'],
                    'attributes'=>['size' => 3,
                                   'placeholder' => 'ID']])
        @endif
                                   
        @include('widgets.form._formitem_text',
                ['name' => 'search_lemma',
                'value' => $url_args['search_lemma'],
                'special_symbol' => true,
                'attributes'=>['size' => 15,
                               'placeholder'=>trans('dict.lemma')]])
                               
        @if ($is_search_wordform)
            @include('widgets.form._formitem_text',
                    ['name' => 'search_wordform',
                    'value' => $url_args['search_wordform'],
                    'special_symbol' => true,
                    'attributes'=>['size' => 15,
                                   'placeholder'=>trans('dict.wordform')]])
        @endif
        
        @include('widgets.form._formitem_text',
                ['name' => 'search_meaning',
                'value' => $url_args['search_meaning'],
                'special_symbol' => true,
                'attributes'=>['size' => 15,
                               'placeholder'=>trans('dict.interpretation')]])
        @include('widgets.form._formitem_select',
                ['name' => 'search_lang',
                 'values' =>$lang_values,
                 'value' =>$url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]])
        @if ($is_search_wordform)
            <br>
            @include('widgets.form._formitem_select',
                    ['name' => 'search_pos',
                     'values' =>$pos_values,
                     'value' =>$url_args['search_pos'],
                     'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 

            @if ($url_args['search_pos'] && $url_args['search_lang'] || $url_args['search_gramset'])         
                @include('widgets.form._formitem_select', 
                        ['name' => 'search_gramset', 
                         'values' =>$gramset_values,
                         'value' =>$url_args['search_gramset'],
                         'attributes'=>['placeholder' => trans('dict.select_gramset') ]]) 
                <br>
            @endif
        @endif
        
        {{trans('messages.show_by')}}
        @include('widgets.form._formitem_text',
                ['name' => 'limit_num',
                'value' => $url_args['limit_num'],
                'attributes'=>['size' => 5,
                               'placeholder' => trans('messages.limit_num') ]]) {{ trans('messages.records') }}
                               
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
        {!! Form::close() !!}

        