        {!! Form::open(['url' => $url,
                        'method' => 'get'])
        !!}
<div class="row condensed">
    <div class="col-sm-2">
        @include('widgets.form._formitem_text',
                ['name' => 'search_lemma',
                 'special_symbol' => true,
                'value' => $url_args['search_lemma'],
                'attributes'=>['placeholder'=>trans('dict.lemma')]])
    </div>
    <div class="col-sm-2">
        @include('widgets.form._formitem_select',
                ['name' => 'search_lang',
                 'values' =>$lang_values,
                 'value' => $url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]])
    </div>
    <div class="col-sm-2">
        @include('widgets.form._formitem_select',
                ['name' => 'search_pos',
                 'values' =>$pos_values,
                 'value' => $url_args['search_pos'],
                 'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 
    </div>
    <div class="col-sm-3">
        @include('widgets.form._formitem_select',
                ['name' => 'search_relation',
                 'values' =>$relation_values,
                 'value' => $url_args['search_relation'],
                 'attributes'=>['placeholder' => trans('dict.select_relation') ]]) 
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

        