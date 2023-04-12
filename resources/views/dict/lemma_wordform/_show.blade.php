        <div class="lemma-b">
            <h3>{{ trans('dict.wordforms') }} 
                <span data-id="{{$lemma->id}}" id="wordform-total">
                @if ($lemma->wordform_total)
                ({{$lemma->wordform_total}})
                @endif
                </span>
            </h3>
            <div class="dict-form">   
                <p>
                @if (User::checkAccess('dict.edit'))
                    <img class="img-loading" id="img-loading_stem-affix" src="{{ asset('images/loading.gif') }}">
                    @include('widgets.form.button._reload', 
                             ['data_reload' => $lemma->id,
                              'class' => 'reload-stem-affix-by-wordforms',
                              'func' => 'reloadStemAffixByWordforms',
                              'title' => trans('dict.reload-stem-affix-by-wordforms')])
                @endif
                    <span id="lemmaStemAffix">{{$lemma->dictForm()}}</span>
                </p>
            </div>
        </div>
            @if (User::checkAccess('dict.edit'))
                {!! Form::open(['url' => '/dict/lemma_wordform/'.$lemma->id.'/edit',
                                'method' => 'get'])
                !!}
                @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
            <div class="row">
                <div class="col-sm-4">
                @include('widgets.form.formitem._select',
                        ['name' => 'dialect_id',
                         'values' =>$dialect_values,
                         ]) 
                </div>
                <div class="col-sm-2">
                @include('widgets.form.formitem._submit', ['title' => trans('messages.edit')])
                </div>
                <div class="col-sm-3">
                @include('widgets.form.button._red', [
                    'id_name' => 'generate-wordforms',
                    'on_click'=>"generateWordforms($lemma->id, [".$lemma->meaningIdsToList()."])", 
                    'title' => trans('dict.generate_wordforms')
                    ])
                </div>
            </div>                 
                {!! Form::close() !!}
            @endif
        

        <img class="img-loading" id="img-loading_wordforms" src="{{ asset('images/loading.gif') }}">
        <div id="wordforms">
        </div>