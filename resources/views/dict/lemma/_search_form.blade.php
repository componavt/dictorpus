        {!! Form::open(['url' => $url,
                        'method' => 'get'])
        !!}
<div class="row">
        @if ($is_search_id)
    <div class="col-sm-1">
            @include('widgets.form._formitem_text',
                    ['name' => 'search_id',
                    'value' => $url_args['search_id'],
                    'attributes'=>['placeholder' => 'ID']])
    </div>
    <div class="col-sm-2">
        @else
    <div class="col-sm-3">
        @endif
        @include('widgets.form._formitem_text',
                ['name' => 'search_lemma',
                'value' => $url_args['search_lemma'],
                'special_symbol' => true,
                'attributes'=>['placeholder'=>trans('dict.lemma')]])
                               
    </div>
        @if ($is_search_wordform)
    <div class="col-sm-3">
            @include('widgets.form._formitem_text',
                    ['name' => 'search_wordform',
                    'value' => $url_args['search_wordform'],
                    'special_symbol' => true,
                    'attributes'=>['placeholder'=>trans('dict.wordform')]])
    </div>
        @endif
        
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
</div>                 
<div class="row">
        @if ($is_search_wordform)
    <div class="col-sm-4">
            @include('widgets.form._formitem_select',
                    ['name' => 'search_pos',
                     'values' =>$pos_values,
                     'value' =>$url_args['search_pos'],
                     'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 
    </div>

            @if ($url_args['search_pos'] && $url_args['search_lang'] || $url_args['search_gramset'])         
    <div class="col-sm-4">
                @include('widgets.form._formitem_select', 
                        ['name' => 'search_gramset', 
                         'values' =>$gramset_values,
                         'value' =>$url_args['search_gramset'],
                         'attributes'=>['placeholder' => trans('dict.select_gramset') ]]) 
    </div>
            @endif
        @endif
        
    <div class="col-sm-4 search-button-b">       
        <span>
        {{trans('messages.show_by')}}
        </span>
        @include('widgets.form._formitem_text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>['placeholder' => trans('messages.limit_num') ]]) 
        <span>
                {{ trans('messages.records') }}
        </span>
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
    </div>
</div>                 
        {!! Form::close() !!}

        