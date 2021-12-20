        {!! Form::open(['url' => $url, 'method' => 'get']) !!}
        <div class="search-form row">
            <div class="col-md-4">
                @include('widgets.form.formitem._select', 
                        ['name' => 'search_lang', 
                         'values' => $lang_values,
                         'value' => $url_args['search_lang'],
                         'title' => trans('dict.lang'),
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
            <div class="col-md-2 search-linked-field">
                @include('widgets.form.formitem._radio_for_field', 
                        ['name' => 'search_linked', 
                         'value' => $url_args['search_linked'],
                         'title'=>trans('corpus.has_link_with_lemma').'?&nbsp;&nbsp;'])
            </div>
            <div class="col-md-4 search-button-b" style='padding-top:25px'>
                <span>
                {{trans('search.show_by')}}
                </span>
                @include('widgets.form.formitem._text', 
                        ['name' => 'limit_num', 
                        'value' => $url_args['limit_num'], 
                        'attributes'=>['placeholder' => trans('messages.limit_num')]]) 
                <span>
                        {{ trans('messages.records') }}
                </span>
                @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
            </div>
        </div>
        {!! Form::close() !!}
