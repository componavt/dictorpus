        {!! Form::open(['url' => $url,
                        'method' => 'get'])
        !!}
<div class="row">
    <div class="col-sm-4">
        @include('widgets.form.formitem._select',
                ['name' => 'search_lang',
                 'values' =>$lang_values,
                 'value' =>$url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]])
    </div>
        
    <div class="col-sm-4">
        @include('widgets.form.formitem._select2',
                ['name' => 'search_dialects',
                 'values' =>$dialect_values,
                 'value' =>$url_args['search_dialects'],
                 'help_func' => "callHelp('help-dialect-usage')",
                 'class'=>'select-dialects form-control']) 
    </div>
        
    <div class="col-sm-4">
            @include('widgets.form.formitem._select',
                    ['name' => 'search_pos',
                     'values' =>$pos_values,
                     'value' =>$url_args['search_pos'],
                     'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 
    </div>
    
    <div class="col-sm-4">
        @include('widgets.form.formitem._text',
                ['name' => 'search_lemma',
                'value' => $url_args['search_lemma'],
                'special_symbol' => true,
                'help_func' => "callHelp('help-text-fields')",
                'attributes'=>['placeholder'=>trans('dict.lemma')]])                               
    </div>
    
    <div class="col-sm-4 search-button-b">       
        <span>{{trans('search.show_by')}}</span>
        @include('widgets.form.formitem._text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>[ 'placeholder' => trans('messages.limit_num') ]]) 
        <span>{{ trans('messages.records') }}</span>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>                 
        {!! Form::close() !!}

        