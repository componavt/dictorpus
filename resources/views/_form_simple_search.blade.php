        @include('widgets.modal',['name'=>'modalHelp',
                                  'title'=>trans('navigation.help'),
                                  'modal_view'=>'help.search_simple'])
                                  
        {!! Form::open(['url' => route($route),
                        'method' => 'get'])
        !!}
        <div class="simple-search-f">
            <div style='width: 100%'>
        @include('widgets.form.formitem._text',
                ['name' => 'search_w',
                'special_symbol' => true,
                'value' => $search_w,
                'help_func' => "callHelp('help-simple')"])    
            </div>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.search')])
        </div>  
                            