        {!! Form::open(['url' => '/corpus/place/', 
                             'method' => 'get']) 
        !!}
<div class="search-form row">
    <div class="col-sm-1">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_id', 
                 'value' => $url_args['search_id'],
                 'attributes'=>['placeholder' => 'ID']])                                  
    </div>
    <div class="col-sm-2">
         @include('widgets.form.formitem._text', 
                ['name' => 'search_name', 
                 'special_symbol' => true,
                 'value' => $url_args['search_name'],
                 'attributes'=>['placeholder' => trans('corpus.title')]])
    </div>
    <div class="col-sm-3">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_region', 
                 'values' => $region_values,
                 'value' => $url_args['search_region'],
                 'attributes' => ['placeholder' => trans('corpus.region')]]) 
    </div>
    <div class="col-sm-3">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_district', 
                 'values' => $district_values,
                 'value' => $url_args['search_district'],
                 'attributes' => ['placeholder' => trans('corpus.district')]]) 
    </div>
    <div class="col-sm-3 search-button-b">       
        <span>
        {{trans('search.show_by')}}
        </span>
        @include('widgets.form.formitem._text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>['size' => 2,
                               'placeholder' => trans('messages.limit_num') ]]) 
        <span>
                {{ trans('messages.records') }}
        </span>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>                 
        {!! Form::close() !!}
