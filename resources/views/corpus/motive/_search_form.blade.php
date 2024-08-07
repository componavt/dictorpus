        {!! Form::open(['url' => '/corpus/motive/', 
                             'method' => 'get']) 
        !!}
<div class="search-form row">
    <div class="col-md-1">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_id', 
                'value' => $url_args['search_id'],
                'attributes'=>['placeholder' => 'ID']])
    </div>
    <div class="col-md-3">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_genre', 
                 'values' => $genre_values,
                 'value' => $url_args['search_genre'],
                 'class'=>'multiple-select-genre form-control'
        ])                 
    </div>
    <div class="col-md-4">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_motype', 
                 'values' => $motype_values,
                 'value' => $url_args['search_motype'],
                 'class'=>'multiple-select-motype form-control'
        ])                 
    </div>
    <div class="col-md-2">
         @include('widgets.form.formitem._text', 
                ['name' => 'search_name', 
                'value' => $url_args['search_name'],
                'attributes'=>['placeholder' => trans('corpus.name')]])
    </div>
    <div class="col-md-2" style="text-align: right">
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>
        {!! Form::close() !!}
