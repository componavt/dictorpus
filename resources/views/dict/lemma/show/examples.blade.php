@if ($sentence_total && (User::checkAccess('dict.edit') || $sentence_count))
                <h4>{{ trans('messages.examples')}} 

                    @if (User::checkAccess('dict.edit'))
                        ({{trans('messages.total')}} {{ $sentence_count}} {{trans('messages.of')}} {{ $sentence_total}})
                        @include('widgets.form.button._edit', 
                                 ['route' => LaravelLocalization::localizeURL('/dict/lemma/'.$meaning->lemma->id.'/edit/examples/'),
                                  'without_text' => 1])
                        @include('widgets.form.button._reload', 
                                 ['data_reload' => $meaning->id,
                                  'class' => 'reload-examples',
                                  'func' => 'reloadExamples',
                                  'title' => trans('messages.reload')])
                    @else
                        ({{ $sentence_count}})
                    @endif
                </h4>
@endif 

@if ($sentence_count)
                <p>
                    @foreach (trans('dict.relevance_scope_example') as $r_k=> $r_v) 
                    <span class='relevance relevance-{{$r_k}}'>
                        <span class="glyphicon glyphicon-star"></span> 
                        {{$r_v}}
                    </span>
                    @endforeach
                </p>

                @include('dict.lemma.show.examples_limit')
                
                <div id="more-{{$meaning->id}}" class="more-examples"></div>   
                
                <img class="img-loading" id="img-loading-more_{{$meaning->id}}" src="{{ asset('images/loading.gif') }}">
                
                <a id="show-more-{{$meaning->id}}" class="show-more-examples" style="display: none"
                   onClick ="showExamples({{$meaning->id}})">
                        {{ trans('dict.more_examples') }}
                </a>
                
                <a id="hide-more-{{$meaning->id}}" class="hide-more-examples" style="display: none"
                   onClick ="hideExamples({{$meaning->id}})">
                        {{ trans('dict.hide_examples') }}
                </a>
                            
@endif
