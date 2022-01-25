        {!! Form::open(['url' => $url, 'method' => 'get']) !!}
        <div class="search-form row">
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
