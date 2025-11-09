        {!! Form::open(['url' => '/corpus/monument/', 
                             'method' => 'get']) 
        !!}
<div class="search-form row">
    <div class="col-sm-4">
         @include('widgets.form.formitem._text', 
                ['name' => 'search_title', 
                'value' => $url_args['search_title'],
                'attributes'=>['placeholder' => trans('corpus.name')]])
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select',
                ['name' => 'search_lang',
                 'values' =>$lang_values,
                 'value' =>$url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.lang')]])
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select2',
                ['name' => 'search_dialect',
                 'values' =>$dialect_values,
                 'value' =>$url_args['search_dialect'],
                 'class'=>'select-dialect form-control']) 
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select',
                ['name' => 'search_type',
                 'values' =>trans('monument.type_values'),
                 'value' =>$url_args['search_type'],
                 'attributes'=>['placeholder' => trans('monument.type')]])
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select',
                ['name' => 'search_is_printed',
                 'values' =>trans('monument.is_printed_values'),
                 'value' =>$url_args['search_is_printed'],
                 'attributes'=>['placeholder' => trans('monument.is_printed')]])
    </div>
    <div class="col-sm-4">
        <div class='flex-hor-group' style="justify-content: flex-start; flex-wrap: nowrap">
            <span style='margin-right: 0.5rem'>{{ trans('messages.from') }}</span>
            @include('widgets.form.formitem._text', 
                    ['name' => 'search_publ_date_from',
                     'value' => $url_args['search_publ_date_from'],
                     'attributes'=> ['placeholder' => 'гггг']])  

            <span style='margin: 0 0.5rem 0 2rem'>{{ trans('monument.to') }}</span>                         
            @include('widgets.form.formitem._text', 
                    ['name' => 'search_publ_date_to',
                     'value' => $url_args['search_publ_date_to'],
                     'attributes'=> ['placeholder' => 'гггг']])     
        </div>
    </div>
    <div class="col-sm-4 search-button-b">       
        <span>
        {{ trans('search.show_by') }}
        </span>
        @include('widgets.form.formitem._text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>['size' => 5,
                               'placeholder' => trans('messages.limit_num') ]]) 
        <span>
                {{ trans('messages.records') }}
        </span>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>                 
        {!! Form::close() !!}
