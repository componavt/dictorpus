        {!! Form::open(['url' => $url, 
                             'method' => 'get']) 
        !!}
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
        @include('widgets.form.formitem._text', 
                ['name' => 'search_lemma', 
                 'special_symbol' => true,
                 'help_func' => "callHelp('help-text-fields')",
                 'value' => $url_args['search_lemma'],
                 'title' => trans('dict.lemma')
                ])                               
    </div>
    <div class="col-md-4">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_informant', 
                 'values' => $informant_values,
                 'value' => $url_args['search_informant'],
                 'title' => trans('dict.speaker'),
        ])                 
    </div>
    @include('widgets.form._search_div')
</div>                 
</div>
        {!! Form::close() !!}
