        {!! Form::open(['url' => '/corpus/informant/', 
                             'method' => 'get']) 
        !!}
<div class="search-form row">
    <div class="col-sm-1">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_id', 
                'value' => $url_args['search_id'],
                'attributes'=>['placeholder' => 'ID']])
    </div>
    <div class="col-sm-6">
         @include('widgets.form.formitem._text', 
                ['name' => 'search_name', 
                'value' => $url_args['search_name'],
                'attributes'=>['placeholder' => trans('corpus.informant_name')]])
    </div>
    <div class="col-sm-1">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_birth', 
                'value' => $url_args['search_birth'],
                'attributes'=>['placeholder' => trans('corpus.birth_year')]])
    </div>
    <div class="col-sm-4 search-button-b">       
        <span>
        {{trans('search.show_by')}}
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
    
    <div class="col-md-4">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_birth_region', 
                 'values' => $region_values,
                 'value' => $url_args['search_birth_region'],
                 'attributes'=>['placeholder' => trans('corpus.region'). ' '. trans('corpus.of_informant_birth')]])
    </div>
    <div class="col-md-4">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_birth_district', 
                 'values' => $district_values,
                 'value' => $url_args['search_birth_district'],
                 'class'=>'select-birth-district form-control'
        ]) 
    </div>    
    <div class="col-md-4">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_birth_place', 
                 'values' => $place_values,
                 'value' => $url_args['search_birth_place'],
                 'class'=>'select-birth-place form-control'
        ]) 
    </div>    
</div>                 
        {!! Form::close() !!}
