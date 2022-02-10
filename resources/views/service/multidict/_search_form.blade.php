        {!! Form::open(['url' => $url, 'method' => 'get']) !!}
        <div class="search-form row">
            <div class="col-md-4">
                @include('widgets.form.formitem._select', 
                        ['name' => 'search_pos', 
                         'values' => $pos_values,
                         'value' => $url_args['search_pos'],
                         'title' => trans('dict.pos'),
                ])                 
            </div>
            <div class="col-sm-4">
                @include('widgets.form.formitem._select', 
                        ['name' => 'search_status', 
                        'values' => trans('dict.output_checked_or_not'),
                        'value' => $url_args['search_status'],
                         'title' => trans('messages.output')] )
            </div>
            <div class="col-md-4 submit-button-b"><br>       
                @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
            </div>
        </div>
        {!! Form::close() !!}
