<?php $for_edition = User::checkAccess('dict.edit');
      $sentence_total = $meaning->countSentences($for_edition); ?>
@if ($sentence_total)
                <h4>{{ trans('messages.examples')}} ({{trans('messages.total')}} {{ $sentence_total}})</h4>
                    @if (User::checkAccess('dict.edit'))
                        {!! Form::open(['route' => ['lemma.update.examples', $lemma->id],
                                        'method' => 'POST',
                                        'class' => 'form-inline'
                                      ])
                        !!}
                    @endif
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
                            @if (User::checkAccess('dict.edit'))
                                @include('widgets.form._formitem_select',
                                        ['name' => 'relevance['.$meaning->id.'_'.$sentence['text']->id.'_'.$sentence['s_id'].'_'.$sentence['w_id'].']',
                                         'values' => trans('dict.relevance_scope'),
                                         'value' => $sentence['relevance']
                                        ])
                            @endif
                                </td>
                                <td> 
                                    {{ $count++ }}.
                                    @include('dict.lemma.show.example_sentence')
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
                    @if (User::checkAccess('dict.edit'))
                        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.save')])
                        {!! Form::close() !!}
                    @endif
@endif
