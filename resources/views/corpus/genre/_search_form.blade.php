        {!! Form::open(['url' => '/corpus/genre/', 
                             'method' => 'get']) 
        !!}
<div class="search-form row">
    <div class="col-md-1">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_id', 
                'value' => $url_args['search_id'],
                'title'  => 'ID',
                'attributes'=>['size' => 3]])
    </div>
    <div class="col-md-5">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_corpus', 
                 'values' => $corpus_values,
                 'value' => $url_args['search_corpus'],
                 'title' => trans('corpus.corpus'),
            ])
    </div>
    <div class="col-md-4">
         @include('widgets.form.formitem._text', 
                ['name' => 'search_name', 
                'value' => $url_args['search_name'],
                'title'  => trans('corpus.name')])
    </div>
    <div class="col-md-2"><br>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>
        {!! Form::close() !!}
