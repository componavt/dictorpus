                <h2>
                    {!!highlight($lemma->lemma, $url_args['search_w'], 'search-word')!!}
                    @if (User::checkAccess('dict.edit'))
                        @include('widgets.form.button._edit', 
                                 ['route' => '/dict/lemma/'.$lemma->id.'/edit',
                                  'without_text' => 1])
                    @endif
                </h2>
