        {!! Form::open(['url' => '/corpus/plot/', 
                             'method' => 'get']) 
        !!}
<div class="search-form row">
    <div class="col-md-4">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_corpus', 
                 'values' => $corpus_values,
                 'value' => $url_args['search_corpus'],
                 'title' => trans('corpus.corpus'),
                 'class'=>'multiple-select-corpus form-control'
            ])
    </div>
    <div class="col-md-4">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_genre', 
                 'values' => $genre_values,
                 'value' => $url_args['search_genre'],
                 'title' => trans('corpus.genre'),
                 'class'=>'multiple-select-genre form-control'
        ])                 
    </div>
    <div class="col-md-4">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_plot', 
                 'values' => $plot_values,
                 'value' => $url_args['search_plot'],
                 'title' => trans('corpus.plot'),
                 'class'=>'select-plot form-control'
        ])                 
    </div>
    <div class="col-md-1">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_id', 
                'value' => $url_args['search_id'] ? $url_args['search_id'] : '',
                'attributes' => ['placeholder'=>'ID']])
    </div>
    <div class="col-md-7">
         @include('widgets.form.formitem._text', 
                ['name' => 'search_name', 
                'value' => $url_args['search_name'],
                'attributes' => ['placeholder'=> trans('corpus.name')]])
    </div>
    @include('widgets.form._search_div')
</div>
        {!! Form::close() !!}
