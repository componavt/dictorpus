<?php $for_edition = User::checkAccess('dict.edit');
      $sentence_count = $meaning->countSentences(false);
      $sentence_total = $meaning->countSentences(true); ?>
@if ($sentence_total && (User::checkAccess('dict.edit') || $sentence_count))
                <h4>{{ trans('messages.examples')}} 

                    @if (User::checkAccess('dict.edit'))
                        ({{trans('messages.total')}} {{ $sentence_count}} {{trans('messages.of')}} {{ $sentence_total}})
                        @include('widgets.form._button_edit', 
                                 ['route' => '/dict/lemma/'.$lemma->id.'/edit/examples/',
                                  'without_text' => 1])
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
<?php $limit = 100; 
      $sentences = $meaning->sentences($for_edition,$limit);
      $count=1; 
      $limit = 5; ?>
                    <table class="lemma-examples">
                    @foreach ($sentences as $sentence)
                        @if ($count==$limit+1)
                        </table>
                        <a id="show-more-{{$meaning->meaning_n}}" 
                           class="show-more-examples"
                           data-for="{{$meaning->meaning_n}}">
                                {{ trans('dict.more_examples') }}
                        </a>
                        <div id="more-{{$meaning->meaning_n}}" class="more-examples">
                        <table class="lemma-examples">
                        @endif
                            <tr class="row">
                                <td> 
                                @if ($sentence['relevance']>0)
                                    {{ $count++ }}.
                                    @include('dict.lemma.show.example_sentence', 
                                        ['relevance'=>$sentence['relevance'], 'is_edit' => 1])
                                @endif
                                </td>
                            </tr>
                    @endforeach
                    </table>
                            
                    @if ($count<=$limit)
                    <div class="more-examples">
                    @else
                    <a class="hide-more-examples"
                       data-for="{{$meaning->meaning_n}}">
                            {{ trans('dict.hide_examples') }}
                    </a>
                    @endif
                    </div>              
@endif
