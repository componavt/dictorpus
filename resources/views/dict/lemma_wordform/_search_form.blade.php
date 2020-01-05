        {!! Form::open(['url' => $url, 'method' => 'get']) !!}
        <div class="row">
            <div class="col-md-3">
                @include('widgets.form.formitem._select', 
                        ['name' => 'search_lang', 
                         'values' => $lang_values,
                         'value' => $url_args['search_lang'],
                         'title' => trans('dict.lang'),
                ])                 
            </div>
            <div class="col-md-3">
                @include('widgets.form.formitem._select', 
                        ['name' => 'search_pos', 
                         'values' => $pos_values,
                         'value' => $url_args['search_pos'],
                         'title' => trans('dict.pos'),
                ])                 
            </div>
            <div class="col-md-3">
            @include('widgets.form.formitem._text',
                    ['name' => 'search_affix',
                    'value' => $url_args['search_affix'],
                    'special_symbol' => true,
                    'title' => trans('dict.affix')])
            </div>
            
            <div class="col-md-3 submit-button-b"><br>       
                @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
            </div>
        </div>
        {!! Form::close() !!}
