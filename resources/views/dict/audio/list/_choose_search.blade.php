    {!! Form::open(['url' => 'dict/audio/list/'.$informant->id.'/choose',
                    'method' => 'get'])
    !!}
    <div class="row">
        <div class="col-sm-4">
            @include('widgets.form.formitem._select',
                    ['name' => 'search_dialect',
                     'values' =>$dialect_values,
                     'value' =>$url_args['search_dialect'],
                     'attributes' => ['placeholder'=>trans('dict.dialect_usage')]]) 
        </div>
        <div class="col-sm-4">
            @include('widgets.form.formitem._text',
                    ['name' => 'search_lemma',
                     'value' =>$url_args['search_lemma'],
                     'attributes' => ['placeholder'=>trans('dict.lemma')]]) 
        </div>
        <div class="col-sm-4">
            @include('widgets.form.formitem._submit', ['title' => trans('messages.search')])
        </div>
    </div>      
    {!! Form::close() !!}
