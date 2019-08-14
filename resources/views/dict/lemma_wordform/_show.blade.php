        <h3>
            {{ trans('dict.wordforms') }}
            @if (User::checkAccess('dict.edit'))
                {!! Form::open(['url' => '/dict/lemma_wordform/'.$lemma->id.'/edit',
                                'method' => 'get'])
                !!}
                @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
            <div class="row">
                <div class="col-sm-3">
                @include('widgets.form.formitem._select',
                        ['name' => 'dialect_id',
                         'values' =>$dialect_values,
                         ]) 
                </div>
                <div class="col-sm-1">
                @include('widgets.form.formitem._submit', ['title' => trans('messages.edit')])
                </div>
            </div>                 
                {!! Form::close() !!}
            @endif
        </h3>

        <img class="img-loading" id="img-loading_wordfrms" src="{{ asset('images/loading.gif') }}">
        <div id="wordforms">
            @include('dict.lemma_wordform._wordform_table')
        </div>