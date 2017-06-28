<?php $for_edition = User::checkAccess('dict.edit');
      $sentence_count = $meaning->countSentences(false);
      $sentence_total = $meaning->countSentences(true); ?>
@if ($sentence_total)
                <h4>{{ trans('messages.examples')}} 
                    ({{trans('messages.total')}} {{ $sentence_count}} {{trans('messages.of')}} {{ $sentence_total}})

                    @if (User::checkAccess('dict.edit'))
                        @include('widgets.form._button_edit', 
                                 ['route' => '/dict/lemma/'.$lemma->id.'/edit/examples/',
                                  'without_text' => 1])
                    @endif
                </h4>
                <p>
                    @foreach (trans('dict.relevance_scope_example') as $r_k=> $r_v) 
                    <span class='relevance relevance-{{$r_k}}'>
                        <span class="glyphicon glyphicon-star"></span> 
                        {{$r_v}}
                    </span>
                    @endforeach
                </p>
{{--                    @if (User::checkAccess('dict.edit'))
                        {!! Form::open(['route' => ['lemma.update.examples', $lemma->id],
                                        'method' => 'POST',
                                        'class' => 'form-inline'
                                      ])
                        !!}
                    @endif --}}
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
{{--                                <td>
                            @if (User::checkAccess('dict.edit'))
                                @include('widgets.form._formitem_select',
                                        ['name' => 'relevance['.$meaning->id.'_'.$sentence['text']->id.'_'.$sentence['s_id'].'_'.$sentence['w_id'].']',
                                         'values' => trans('dict.relevance_scope'),
                                         'value' => $sentence['relevance']
                                        ])
                            @endif 
                                </td>--}}
                                <td> 
                                @if ($sentence['relevance']>0)
                                    {{ $count++ }}.
                                    @include('dict.lemma.show.example_sentence', ['relevance'=>$sentence['relevance']])
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
{{--                    @if (User::checkAccess('dict.edit'))
                        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.save')])
                        {!! Form::close() !!}
                    @endif --}}
@endif
