        {!! Form::open(['url' => '/dict/gramset/', 
                             'method' => 'get']) 
        !!}

<div class="row">
    <div class="col-sm-4">
        @include('widgets.form._formitem_select', 
                ['name' => 'search_pos', 
                 'values' =>$pos_values,
                 'value' => $url_args['search_pos'],
                 'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 
    </div>
    <div class="col-sm-4">
        @include('widgets.form._formitem_select', 
                ['name' => 'search_lang', 
                 'values' =>$lang_values,
                 'value' => $url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]]) 
    </div>
    <div class="col-sm-4 search-button-b">       
        <span>
        {{trans('messages.show_by')}}
        </span>
        @include('widgets.form._formitem_text', 
                ['name' => 'limit_num', 
                 'value' => $url_args['limit_num'], 
                 'attributes'=>['size' => 5,
                                'placeholder' => trans('messages.limit_num') ]]) 
        <span>
                {{ trans('messages.records') }}
        </span>
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
    </div>
</div>                 
        {!! Form::close() !!}
