        {!! Form::open(['url' => '/corpus/text/', 
                             'method' => 'get']) 
        !!}
<div class="row">
    <div class="col-md-4">
        @include('widgets.form._formitem_select2', 
                ['name' => 'search_lang', 
                 'values' => $lang_values,
                 'value' => $url_args['search_lang'],
                 'title' => trans('dict.lang'),
                 'class'=>'multiple-select-lang form-control',
        ])
                 
    </div>
    <div class="col-md-4">
        @include('widgets.form._formitem_select2',
                ['name' => 'search_dialect', 
                 'values' =>$dialect_values,
                 'value' => $url_args['search_dialect'],
                 'title' => trans('dict.dialect'),
                 'class'=>'multiple-select-dialect form-control'
            ])
    </div>
    <div class="col-md-4">
        @include('widgets.form._formitem_select2', 
                ['name' => 'search_corpus', 
                 'values' => $corpus_values,
                 'value' => $url_args['search_corpus'],
                 'title' => trans('corpus.corpus'),
                 'class'=>'multiple-select-corpus form-control'
            ])
    </div>
</div>                 
<div class="row">
    <div class="col-md-4">
        @include('widgets.form._formitem_text', 
                ['name' => 'search_title', 
                 'special_symbol' => true,
                'value' => $url_args['search_title'],
                'attributes'=>['placeholder' => trans('corpus.title')]])
                               
    </div>
    <div class="col-md-4">
        @include('widgets.form._formitem_text', 
                ['name' => 'search_word', 
                 'special_symbol' => true,
                'value' => $url_args['search_word'],
                'attributes'=>['placeholder' => trans('corpus.word')]])
                               
    </div>
    <div class="col-md-4 search-button-b">       
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
