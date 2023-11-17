        {!! Form::open(['url' => $url,
                        'method' => 'get'])
        !!}
<div class="show-search-form">{{trans('search.advanced_search')}} &#8595;</div>
<div class="row search-form">
    <div class="col-sm-4">
        @include('widgets.form.formitem._text',
                ['name' => 'search_lemma',
                'value' => $url_args['search_lemma'],
                'special_symbol' => true,
                'help_func' => "callHelp('help-text-fields')",
                'title'=> trans('dict.lemma')])                               
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select',
                ['name' => 'search_lang',
                 'values' =>$lang_values,
                 'value' =>$url_args['search_lang'],
                 'title'=> trans('dict.lang') ])
    </div>
    <div class="col-sm-4{{sizeof($url_args['search_dialects']) ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select2',
                ['name' => 'search_dialects',
                 'values' =>$dialect_values,
                 'value' =>$url_args['search_dialects'],
                 'help_func' => "callHelp('help-dialect-usage')",
                 'title' => trans('dict.dialect'),
                 'class'=>'select-dialects form-control']) 
    </div>
    <div class="col-sm-4{{$url_args['search_pos'] ? '' : ' ext-form'}}">
            @include('widgets.form.formitem._select',
                    ['name' => 'search_pos',
                     'values' =>$pos_values,
                     'value' =>$url_args['search_pos'],
                     'title' => trans('dict.pos') ]) 
    </div>
    <div class="col-sm-4{{$url_args['search_concept_category'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select',
                ['name' => 'search_concept_category',
                 'values' => $concept_category_values,
                 'value' =>$url_args['search_concept_category'],
                 'title'=> trans('dict.concept_category') ]) 
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select2',
                ['name' => 'search_concept', 
                 'is_multiple' => false,
                 'values' => $concept_values,
                 'value' => $url_args['search_concept'],
                 'title' => trans('dict.concept'),
                 'class'=>'select-concept form-control']) 
    </div>
    <div class="col-sm-4{{$url_args['search_meaning'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._text',
                ['name' => 'search_meaning',
                'value' => $url_args['search_meaning'],
                'special_symbol' => true,
                'help_func' => "callHelp('help-text-fields')",
                'title'=>trans('dict.interpretation')])
    </div>
    <div class="col-sm-1{{$url_args['search_id'] ? '' : ' ext-form'}}">
            @include('widgets.form.formitem._text',
                    ['name' => 'search_id',
                    'value' => $url_args['search_id'],
                    'title'=> 'ID'])
    </div>
    <div class="col-sm-3{{$url_args['with_examples'] || $url_args['with_audios'] ? '' : ' ext-form'}}" style='padding-top: 10px'>
        @include('widgets.form.formitem._checkbox',
                ['name' => 'with_examples',
                'value' => 1,
                'checked' => $url_args['with_examples']==1,
                'tail'=>trans('dict.with_examples')]) 
        @include('widgets.form.formitem._checkbox',
                ['name' => 'with_audios',
                'value' => 1,
                'checked' => $url_args['with_audios']==1,
                'tail'=>trans('dict.with_audios')]) 
    </div>        
    <div class="col-sm-2 ext-form" style='padding-top: 25px'>     
        <div class='search-button-b'>
        <span>{{trans('search.show_by')}}</span>
        @include('widgets.form.formitem._text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>[ 'placeholder' => trans('messages.limit_num') ]]) 
        <span>{{ trans('messages.records') }}</span>
        </div>
    </div>        
    <div class="col-sm-2 margin-to-ext">       
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>      
<div class="hide-search-form">{{trans('search.simple_search')}} &#8593;</div>
        {!! Form::hidden('search_label', $url_args['search_label']) !!}        
        {!! Form::close() !!}

        