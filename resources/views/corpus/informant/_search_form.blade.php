        {!! Form::open(['url' => '/corpus/informant/', 
                             'method' => 'get']) 
        !!}
<div class="row">
    <div class="col-sm-1">
        @include('widgets.form._formitem_text', 
                ['name' => 'search_id', 
                'value' => $search_id,
                'attributes'=>['placeholder' => 'ID']])
    </div>
    <div class="col-sm-2">
         @include('widgets.form._formitem_text', 
                ['name' => 'informant_name', 
                'value' => $informant_name,
                'attributes'=>['placeholder' => trans('corpus.informant_name')]])
    </div>
    <div class="col-sm-5">
        @include('widgets.form._formitem_select', 
                ['name' => 'birth_place_id', 
                 'values' => $place_values,
                 'value' => $birth_place_id,
                 'attributes' => ['placeholder' => trans('corpus.birth_place')]])                                   
    </div>
    <div class="col-sm-1">
        @include('widgets.form._formitem_text', 
                ['name' => 'birth', 
                'value' => $birth,
                'attributes'=>['placeholder' => trans('corpus.birth_year')]])
    </div>
    <div class="col-sm-3 search-button-b">       
        <span>
        {{trans('messages.show_by')}}
        </span>
        @include('widgets.form._formitem_text', 
                ['name' => 'limit_num', 
                'value' => $limit_num, 
                'attributes'=>['size' => 5,
                               'placeholder' => trans('messages.limit_num') ]]) 
        <span>
                {{ trans('messages.records') }}
        </span>
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
    </div>
</div>                 
        {!! Form::close() !!}
