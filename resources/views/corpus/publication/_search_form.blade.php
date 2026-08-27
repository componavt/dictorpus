        {!! Form::open(['url' => '/corpus/publication/', 
                             'method' => 'get']) 
        !!}
        <div class="search-form row">
            <div class="col-sm-2">
            @include('widgets.form.formitem._text', 
                ['name' => 'search_authors', 
                'value' => $url_args['search_authors'] ?? '',
                'title'=>trans('corpus.authors')
                ])
            </div>
            <div class="col-sm-2">
            @include('widgets.form.formitem._text', 
                ['name' => 'search_title', 
                'value' => $url_args['search_title'] ?? '',
                'title'=> trans('corpus.title')
                ])
            </div>
            <div class="col-md-2">
            @include('widgets.form.formitem._text', 
                    ['name' => 'search_year_from', 
                     'value' => $url_args['search_year_from'] ?? '',
                     'title' => trans('search.year_from')
                    ])                               
            </div>
            <div class="col-md-2">
            @include('widgets.form.formitem._text', 
                    ['name' => 'search_year_to', 
                     'help_func' => 'callHelpYear()',
                     'value' => $url_args['search_year_to'] ?? '',
                     'title' => trans('search.year_to')
                    ])                               
            </div>
            @include('widgets.form._search_div', ['rows'=>2])
        </div>
        {!! Form::close() !!}
