        {!! Form::open(['url' => '/corpus/text/', 
                             'method' => 'get']) 
        !!}
<div class="show-search-form">{{trans('search.advanced_search')}} &#8595;</div>
<div class="search-form search-text">        
<div class="row">
    <div class="col-md-4">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_lang', 
                 'values' => $lang_values,
                 'value' => $url_args['search_lang'],
                 'title' => trans('dict.lang'),
                 'class'=>'multiple-select-lang form-control',
        ])                 
    </div>
    <div class="col-md-4">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_corpus', 
                 'values' => $corpus_values,
                 'value' => $url_args['search_corpus'],
                 'title' => trans('corpus.corpus'),
                 'class'=>'multiple-select-corpus form-control'
            ])
    </div>
    <div class="col-md-4{{$url_args['search_informant'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_informant', 
                 'values' => $informant_values,
                 'value' => $url_args['search_informant'],
                 'title' => trans('corpus.informant'),
        ])                 
    </div>
    <div class="col-md-4{{sizeof($url_args['search_dialect']) ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select2',
                ['name' => 'search_dialect', 
                 'values' =>$dialect_values,
                 'value' => $url_args['search_dialect'],
                 'title' => trans('dict.dialect'),
                 'class'=>'multiple-select-dialect form-control'
            ])
    </div>
    <div class="col-md-4{{sizeof($url_args['search_genre']) ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_genre', 
                 'values' => $genre_values,
                 'value' => $url_args['search_genre'],
                 'title' => trans('corpus.genre'),
                 'class'=>'multiple-select-genre form-control'
        ])                 
    </div>
    <div class="col-md-4{{$url_args['search_recorder'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_recorder', 
                 'values' => $recorder_values,
                 'value' => $url_args['search_recorder'],
                 'title' => trans('corpus.recorder'),
        ])                 
    </div>
    <div class="col-md-4{{$url_args['search_title'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_title', 
                 'special_symbol' => true,
                 'help_func' => "callHelp('help-text-fields')",
                 'value' => $url_args['search_title'],
                 'title' => trans('corpus.title')
                ])                               
    </div>
    <div class="col-md-4{{$url_args['search_author'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_author', 
                 'values' => $author_values,
                 'value' => $url_args['search_author'],
                 'title' => trans('search.author_or_trans'),
        ])                 
    </div>
    <div class="col-md-2{{$url_args['search_year_from'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_year_from', 
                 'value' => $url_args['search_year_from'] ? $url_args['search_year_from'] : '',
                 'title' => trans('search.year_from')
                ])                               
    </div>
    <div class="col-md-2{{$url_args['search_year_to'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_year_to', 
                 'help_func' => 'callHelpYear()',
                 'value' => $url_args['search_year_to'] ? $url_args['search_year_to'] : '',
                 'title' => trans('search.year_to')
                ])                               
    </div>
{{--    <div class="col-md-4{{$url_args['search_word'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_word', 
                 'special_symbol' => true,
                 'help_func' => "callHelp('help-text-fields')",
                 'value' => $url_args['search_word'],
                 'title'=> trans('corpus.word')
                ])
                               
    </div>--}}
    <div class="col-md-4{{$url_args['search_source'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_source', 
                 'special_symbol' => true,
                 'help_func' => "callHelp('help-source')",
                 'value' => $url_args['search_source'],
                 'title'=> trans('corpus.source')
                ])
                               
    </div>
    <div class="col-md-4{{$url_args['search_text'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_text', 
                 'special_symbol' => true,
                 'help_func' => "callHelp('help-text-fields')",
                 'value' => $url_args['search_text'],
                 'title' => trans('corpus.text_fragment')
                ])
                               
    </div>
    <div class="col-md-4 search-button-b">       
        <span>
        {{trans('search.show_by')}}
        </span>
        @include('widgets.form.formitem._text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>['placeholder' => trans('messages.limit_num') ]]) 
        <span>
                {{ trans('messages.records') }}
        </span>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>                 
</div>
<div class="hide-search-form">{{trans('search.simple_search')}} &#8593;</div>
        {!! Form::close() !!}
