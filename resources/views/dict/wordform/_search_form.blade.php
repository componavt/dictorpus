        {!! Form::open(['url' => '/dict/wordform/', 
                             'method' => 'get']) 
        !!}
        
<div class="row">
    <div class="col-sm-3">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_wordform', 
                 'special_symbol' => true,
                'value' => $url_args['search_wordform'],
                'attributes'=>['placeholder'=>trans('dict.wordform')]])
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_lang', 
                 'values' =>$lang_values,
                 'value' =>$url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]]) 
    </div>
    <div class="col-sm-5">
        @include('widgets.form.formitem._select2',
                ['name' => 'search_dialect', 
                 'values' =>$dialect_values,
                 'value' =>[$url_args['search_dialect']],
                 'is_multiple' => false,
                 'class'=>'select-dialect form-control'])
    </div>
</div>    
<div class="row">
    <div class="col-sm-3">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_pos', 
                 'values' =>$pos_values,
                 'value' =>$url_args['search_pos'],
                 'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 
    </div>
    <div class="col-sm-5">
        @include('widgets.form.formitem._select2',
                ['name' => 'search_gramset', 
                 'values' =>$gramset_values,
                 'value' =>[$url_args['search_gramset']],
                 'is_multiple' => false,
                 'class'=>'select-gramset form-control'])
    </div>
    <div class="col-sm-4 search-button-b">       
        <span>
        {{trans('messages.show_by')}}
        </span>
        @include('widgets.form.formitem._text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>['size' => 5, 'placeholder' => trans('messages.limit_num') ]]) 
        <span>
                {{ trans('messages.records') }}
        </span>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>    

        {!! Form::close() !!}
