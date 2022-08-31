        {!! Form::open(['url' => $form_url, 
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
    <div class="col-md-4{{sizeof($url_args['search_dialect']) ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select2',
                ['name' => 'search_dialect', 
                 'values' =>$dialect_values,
                 'value' => $url_args['search_dialect'],
                 'title' => trans('dict.dialect'),
                 'class'=>'multiple-select-dialect form-control'
            ])
    </div>
@if (!$full)    
    <div class="col-md-4{{$url_args['search_title'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_title', 
                 'special_symbol' => true,
                 'help_func' => "callHelp('help-text-fields')",
                 'value' => $url_args['search_title'],
                 'title' => trans('corpus.title')
                ])                               
    </div>
@else
    <div class="col-md-4">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_corpus', 
                 'values' => $corpus_values,
                 'value' => $url_args['search_corpus'],
                 'title' => trans('corpus.corpus'),
                 'class'=>'multiple-select-corpus form-control'
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
    <div class="col-md-4{{sizeof($url_args['search_plot']) ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_plot', 
                 'values' => $plot_values,
                 'value' => $url_args['search_plot'],
                 'title' => trans('corpus.plot'),
                 'class'=>'multiple-select-plot form-control'
        ])                 
    </div>
    <div class="col-md-4{{sizeof($url_args['search_topic']) ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_topic', 
                 'values' => $topic_values,
                 'value' => $url_args['search_topic'],
                 'title' => trans('corpus.topic'),
                 'class'=>'select-topic form-control'
        ])                 
    </div>
@endif
    <div class="col-md-4{{$url_args['search_region'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_region', 
                 'values' => $region_values,
                 'value' => $url_args['search_region'],
                 'title' => trans('corpus.region'). ' '. trans('corpus.of_recording')]) 
    </div>
    <div class="col-md-4{{sizeof($url_args['search_district']) ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_district', 
                 'values' => $district_values,
                 'value' => $url_args['search_district'],
                 'title' => trans('corpus.district'). ' '. trans('corpus.of_recording'),
                 'class'=>'select-district form-control'
        ]) 
    </div>    
    <div class="col-md-4{{sizeof($url_args['search_place']) ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_place', 
                 'values' => $place_values,
                 'value' => $url_args['search_place'],
                 'title' => trans('corpus.place'). ' '. trans('corpus.of_recording'),
                 'class'=>'select-place form-control'
        ]) 
    </div>    
    
    <div class="col-md-4{{$url_args['search_birth_region'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_birth_region', 
                 'values' => $region_values,
                 'value' => $url_args['search_birth_region'],
                 'title' => trans('corpus.region'). ' '. trans('corpus.of_informant_birth')]) 
    </div>
    <div class="col-md-4{{sizeof($url_args['search_birth_district']) ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_birth_district', 
                 'values' => $district_values,
                 'value' => $url_args['search_birth_district'],
                 'title' => trans('corpus.district'). ' '. trans('corpus.of_informant_birth'),
                 'class'=>'select-birth-district form-control'
        ]) 
    </div>    
    <div class="col-md-4{{sizeof($url_args['search_birth_place']) ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_birth_place', 
                 'values' => $place_values,
                 'value' => $url_args['search_birth_place'],
                 'title' => trans('corpus.place'). ' '. trans('corpus.of_informant_birth'),
                 'class'=>'select-birth-place form-control'
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
    <div class="col-md-4{{$url_args['search_recorder'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_recorder', 
                 'values' => $recorder_values,
                 'value' => $url_args['search_recorder'],
                 'title' => trans('corpus.recorder'),
        ])                 
    </div>
@if ($full)    
    <div class="col-md-4{{$url_args['search_author'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_author', 
                 'values' => $author_values,
                 'value' => $url_args['search_author'],
                 'title' => trans('search.author_or_trans'),
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
@endif    
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
    <div class="col-md-4{{$url_args['search_source'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_source', 
                 'special_symbol' => true,
                 'help_func' => "callHelp('help-source')",
                 'value' => $url_args['search_source'],
                 'title'=> trans('corpus.source')
                ])
                               
    </div>
@if ($full)    
    <div class="col-md-4{{$url_args['search_text'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_text', 
                 'special_symbol' => true,
                 'help_func' => "callHelp('help-text_fragment')",
                 'value' => $url_args['search_text'],
                 'title' => trans('corpus.text_fragment')
                ])
                               
    </div>
    <div class="col-sm-4{{$url_args['with_audio'] ? '' : ' ext-form'}}" style='padding-top: 25px'>
        @include('widgets.form.formitem._checkbox',
                ['name' => 'with_audio',
                'value' => 1,
                'checked' => $url_args['with_audio']==1,
                'tail'=>trans('corpus.with_audio')]) 
    </div>    
@endif
    @include('widgets.form._search_div')
</div>                 
</div>
<div class="hide-search-form">{{trans('search.simple_search')}} &#8593;</div>
        {!! Form::close() !!}
