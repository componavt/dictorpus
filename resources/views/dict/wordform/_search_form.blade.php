        {!! Form::open(['url' => '/dict/wordform/', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form._formitem_text', 
                ['name' => 'search_wordform', 
                 'special_symbol' => true,
                'value' => $url_args['search_wordform'],
                'attributes'=>['size' => 15,
                               'placeholder'=>trans('dict.wordform')]])
                               
        @include('widgets.form._formitem_select', 
                ['name' => 'search_pos', 
                 'values' =>$pos_values,
                 'value' =>$url_args['search_pos'],
                 'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 
                 
        @include('widgets.form._formitem_select', 
                ['name' => 'search_lang', 
                 'values' =>$lang_values,
                 'value' =>$url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]]) 
                 
        @include('widgets.form._formitem_select', 
                ['name' => 'search_dialect', 
                 'values' =>$dialect_values,
                 'value' =>$url_args['search_dialect'],
                 'attributes'=>['placeholder' => trans('dict.select_dialect') ]]) 
        <br>         
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
        @include('widgets.form._formitem_text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>['size' => 5,
                               'placeholder' => trans('messages.limit_num') ]]) {{ trans('messages.records') }}
        {!! Form::close() !!}
