        {!! Form::open(['url' => '/corpus/district/', 
                             'method' => 'get']) 
        !!}
<div class="search-form row">
    <div class="col-sm-1">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_id', 
                'value' => $url_args['search_id'],
                'attributes'=>['placeholder' => 'ID']])
    </div>
    <div class="col-sm-3">
         @include('widgets.form.formitem._text', 
                ['name' => 'search_name', 
                'value' => $url_args['search_name'],
                'attributes'=>['placeholder' => trans('corpus.name')]])
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_region', 
                 'values' => $region_values,
                 'value' => $url_args['search_region'],
                 'attributes' => ['placeholder' => trans('corpus.region')]]) 
    </div> 
    @include('widgets.form._search_div')
</div>
        {!! Form::close() !!}
