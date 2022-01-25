        <div class="lemma-b">
            <div>
                <h2>
                    {{ $lemma->lemma }}
                    @if (User::checkAccess('dict.edit'))
                        @include('widgets.form.button._edit', 
                                 ['route' => '/dict/lemma/'.$lemma->id.'/edit',
                                  'without_text' => 1])
                    @endif
                </h2>
            </div>
            <div class="dict-form">   
                <p><span id="lemmaStemAffix">{{$lemma->dictForm()}}</span>
            @if (User::checkAccess('dict.edit'))
                <img class="img-loading" id="img-loading_stem-affix" src="{{ asset('images/loading.gif') }}">
                @include('widgets.form.button._reload', 
                         ['data_reload' => $lemma->id,
                          'class' => 'reload-stem-affix-by-wordforms',
                          'func' => 'reloadStemAffixByWordforms',
                          'title' => trans('dict.reload-stem-affix-by-wordforms')])
            @endif
                </p>
            </div>
        </div>
