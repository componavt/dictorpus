                <h2>
                    {{ $lemma->lemma }}
                    @if (User::checkAccess('dict.edit'))
                        @include('widgets.form.button._edit', 
                                 ['route' => '/dict/lemma/'.$lemma->id.'/edit',
                                  'without_text' => 1])
                    @endif
                </h2>
