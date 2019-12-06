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
            <div class="col-md-2 submit-button-b"><br>       
                @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
            </div>
            <div class="col-md-3 search-button-b" style="padding-top: 20px;">
                @include('widgets.form.formitem._text', 
                        ['name' => 'limit_num', 
                        'value' => $url_args['limit_num'], 
                        'attributes'=>['placeholder' => trans('messages.limit_num')]]) 
                <span>
                        {{ trans('messages.records') }}
                </span>
            </div>
        </div>
        {!! Form::close() !!}
