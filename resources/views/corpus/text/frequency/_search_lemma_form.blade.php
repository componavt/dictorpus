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
            <div class="col-md-4">
                @include('widgets.form.formitem._select2',
                    ['name' => 'search_dialect', 
                     'values' =>$dialect_values,
                     'value' => $url_args['search_dialect'],
                     'title' => trans('dict.dialect'),
                     'is_multiple' => false,
                     'class'=>'select-dialect form-control'
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
            <div class="col-md-2 submit-button-b"><br>       
                @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
            </div>
        </div>
        {!! Form::close() !!}
