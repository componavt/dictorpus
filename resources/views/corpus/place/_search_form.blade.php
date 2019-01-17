        {!! Form::open(['url' => '/corpus/place/', 
                             'method' => 'get']) 
        !!}
<div class="row">
    <div class="col-sm-1">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_id', 
                'value' => $search_id,
                'attributes'=>['placeholder' => 'ID']])                                  
    </div>
    <div class="col-sm-2">
         @include('widgets.form.formitem._text', 
                ['name' => 'place_name', 
                 'special_symbol' => true,
                'value' => $place_name,
                'attributes'=>['placeholder' => trans('corpus.title')]])
    </div>
    <div class="col-sm-3">
        @include('widgets.form.formitem._select', 
                ['name' => 'region_id', 
                 'values' => $region_values,
                 'value' => $region_id,
                 'attributes' => ['placeholder' => trans('corpus.region')]]) 
    </div>
    <div class="col-sm-3">
        @include('widgets.form.formitem._select', 
                ['name' => 'district_id', 
                 'values' => $district_values,
                 'value' => $district_id,
                 'attributes' => ['placeholder' => trans('corpus.district')]]) 
    </div>
    <div class="col-sm-3 search-button-b">       
        <span>
        {{trans('messages.show_by')}}
        </span>
        @include('widgets.form.formitem._text', 
                ['name' => 'limit_num', 
                'value' => $limit_num, 
                'attributes'=>['size' => 5,
                               'placeholder' => trans('messages.limit_num') ]]) 
        <span>
                {{ trans('messages.records') }}
        </span>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>                 
        {!! Form::close() !!}
