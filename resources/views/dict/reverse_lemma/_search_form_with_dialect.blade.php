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
                ['name' => 'search_dialect', 
                 'values' =>$dialect_values,
                 'value' => $url_args['search_dialect'],
                 'is_multiple' => false,
                 'class'=>'select-dialect form-control',
                 'attributes'=>['placeholder' => trans('dict.select_dialect') ]
            ])
    </div>
        
    <div class="col-sm-4">
            @include('widgets.form.formitem._select',
                    ['name' => 'search_pos',
                     'values' =>$pos_values,
                     'value' =>$url_args['search_pos'],
                     'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 
    </div>
    
    <div class="col-sm-8">
        @include('widgets.form.formitem._checkbox', ['name' => 'join_harmony', 'title' => trans('dict.join_harmony')] )
    </div>
    
    <div class="col-sm-4 search-button-b">       
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>                 
        {!! Form::close() !!}

        