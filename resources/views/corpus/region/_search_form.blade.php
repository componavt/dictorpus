        {!! Form::open(['url' => '/corpus/region/', 
                             'method' => 'get']) 
        !!}
<div class="search-form row">
    <div class="col-sm-2">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_id', 
                'value' => $search_id,
                'attributes'=>['placeholder' => 'ID']])
    </div>
    <div class="col-sm-8">
         @include('widgets.form.formitem._text', 
                ['name' => 'region_name', 
                'value' => $region_name,
                'attributes'=>['placeholder' => trans('corpus.name')]])
    </div>
    <div class="col-sm-2" style='text-align: right'>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>
        {!! Form::close() !!}
