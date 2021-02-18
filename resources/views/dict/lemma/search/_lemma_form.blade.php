        {!! Form::open(['url' => $url,
                        'method' => 'get'])
        !!}
<div class="show-search-form">Расширенный поиск &#8595;</div>
<div class="row search-form">
    <div class="col-sm-1">
            @include('widgets.form.formitem._text',
                    ['name' => 'search_id',
                    'value' => $url_args['search_id'],
                    'attributes'=>['placeholder' => 'ID']])
    </div>
    <div class="col-sm-3">
        @include('widgets.form.formitem._text',
                ['name' => 'search_lemma',
                'value' => $url_args['search_lemma'],
                'special_symbol' => true,
                'attributes'=>['placeholder'=>trans('dict.lemma')]])                               
    </div>
    <div class="col-sm-4{{$url_args['search_wordform'] ? '' : ' ext-form'}}">
            @include('widgets.form.formitem._text',
                    ['name' => 'search_wordform',
                    'value' => $url_args['search_wordform'],
                    'special_symbol' => true,
                    'attributes'=>['placeholder'=>trans('dict.wordform')]])
    </div>
    <div class="col-sm-4{{$url_args['search_meaning'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._text',
                ['name' => 'search_meaning',
                'value' => $url_args['search_meaning'],
                'special_symbol' => true,
                'attributes'=>['placeholder'=>trans('dict.interpretation')]])
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select',
                ['name' => 'search_lang',
                 'values' =>$lang_values,
                 'value' =>$url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]])
    </div>
    <div class="col-sm-4{{$url_args['search_pos'] ? '' : ' ext-form'}}">
            @include('widgets.form.formitem._select',
                    ['name' => 'search_pos',
                     'values' =>$pos_values,
                     'value' =>$url_args['search_pos'],
                     'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 
    </div>
    @if ($url_args['search_pos'] && $url_args['search_lang'] || $url_args['search_gramset'])         
    <div class="col-sm-4{{$url_args['search_gramset'] ? '' : ' ext-form'}}">
                @include('widgets.form.formitem._select', 
                        ['name' => 'search_gramset', 
                         'values' =>$gramset_values,
                         'value' =>$url_args['search_gramset'],
                         'attributes'=>['placeholder' => trans('dict.select_gramset') ]]) 
    </div>
    @endif
    <div class="col-sm-4{{sizeof($url_args['search_dialects']) ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select2',
                ['name' => 'search_dialects',
                 'values' =>$dialect_values,
                 'value' =>$url_args['search_dialects'],
                 'class'=>'select-dialects form-control']) 
    </div>
    <div class="col-sm-4{{$url_args['search_concept_category'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select',
                ['name' => 'search_concept_category',
                 'values' => $concept_category_values,
                 'value' =>$url_args['search_concept_category'],
                 'attributes'=>['placeholder' => trans('dict.select_concept_category') ]]) 
    </div>
    <div class="col-sm-4{{$url_args['search_concept'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select2',
                ['name' => 'search_concept', 
                 'is_multiple' => false,
                 'values' => $concept_values,
                 'value' => $url_args['search_concept'],
                 'class'=>'select-concept form-control']) 
    </div>
        
    <div class="col-sm-4 search-button-b">       
        <span>{{trans('messages.show_by')}}</span>
        @include('widgets.form.formitem._text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>[ 'placeholder' => trans('messages.limit_num') ]]) 
        <span>{{ trans('messages.records') }}</span>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>      
<div class="hide-search-form">Простой поиск &#8593;</div>
        {!! Form::hidden('search_label', $url_args['search_label']) !!}        
        {!! Form::close() !!}

        