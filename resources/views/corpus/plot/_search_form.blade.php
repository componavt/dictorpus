        {!! Form::open(['url' => '/corpus/plot/', 
                             'method' => 'get']) 
        !!}
<div class="search-form row">
    <div class="col-md-3">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_corpus', 
                 'values' => $corpus_values,
                 'value' => $url_args['search_corpus'],
                 'title' => trans('corpus.corpus'),
                 'class'=>'multiple-select-corpus form-control'
            ])
    </div>
    <div class="col-md-3">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_genre', 
                 'values' => $genre_values,
                 'value' => $url_args['search_genre'],
                 'title' => trans('corpus.genre'),
                 'class'=>'multiple-select-genre form-control'
        ])                 
    </div>
    <div class="col-md-1">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_id', 
                'value' => $url_args['search_id'] ? $url_args['search_id'] : '',
                'title' => 'ID'])
    </div>
    <div class="col-md-3">
         @include('widgets.form.formitem._text', 
                ['name' => 'search_name', 
                'value' => $url_args['search_name'],
                'title'=> trans('corpus.name')])
    </div>
    <div class="col-md-2" style='text-align:right; padding-top:27px'>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>
        {!! Form::close() !!}
