        {!! Form::open(['url' => $url,
                        'method' => 'get'])
        !!}
<div class="row">
    <div class="col-sm-3">
        @include('widgets.form._formitem_text',
                ['name' => 'search_lemma',
                'value' => $url_args['search_lemma'],
                'special_symbol' => true,
                'attributes'=>['placeholder'=>trans('dict.lemma')]])
                               
    </div>        
    <div class="col-sm-3">
        @include('widgets.form._formitem_text',
                ['name' => 'search_meaning',
                'value' => $url_args['search_meaning'],
                'special_symbol' => true,
                'attributes'=>['placeholder'=>trans('dict.interpretation')]])
    </div>
    <div class="col-sm-3">
        @include('widgets.form._formitem_select',
                ['name' => 'search_lang',
                 'values' =>$lang_values,
                 'value' =>$url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]])
    </div>        
    <div class="col-sm-3 search-button-b">       
        <span>
        {{trans('messages.show_by')}}
        </span>
        @include('widgets.form._formitem_text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>['size' => 5, 'placeholder' => trans('messages.limit_num') ]]) 
        <span>
                {{ trans('messages.records') }}
        </span>
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
    </div>
</div>                 
        {!! Form::close() !!}

        