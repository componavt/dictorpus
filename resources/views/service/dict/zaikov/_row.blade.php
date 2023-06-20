            <tr id="row-{{$lemma->id}}">
                <td data-th="No">{{ $list_count ?? '' }}</td>
                <td data-th="{{ trans('dict.lemma') }}">
                    <a href="{{ LaravelLocalization::localizeURL("/dict/lemma/".$lemma->id) }}">
                       <span id='b-lemma-{{ $lemma->id }}'>{{$lemma->zaikovTemplate()}}</span>
                    </a>
                </td>
                <td data-th="{{ trans('dict.pos') }}">
                        {{$lemma->pos ? $lemma->pos->dict_code : ''}}
                </td>
                <td data-th="{{ trans('dict.meanings') }}" id='meanings-{{ $lemma->id }}'>
                    @include('service.dict.lemma._meanings')
                </td>
                <td data-th="{{ trans('messages.actions') }}" style="text-align:right">
                    @if ($lemma->wordforms()->wherePivot('dialect_id', $dialect_id)->count()) 
                        <a onclick="viewWordforms({{ $lemma->id }}, {{$dialect_id}})" 
                           title="Просмотреть словоформы"
                           style="font-weight: bold; cursor: pointer">W</a>
                    @endif
                    <i class="fa fa-pencil-alt fa-lg clickable link-color" 
                       onClick="editLemma({{ $lemma->id }}, '{{ $lemma->lemma }}', {{ $lemma->pos_id }}, 
                               {{ $lemma->features && $lemma->features->number ? $lemma->features->number : 0 }}, 
                               {{ $lemma->features && $lemma->features->reflexive ? 1 : 0 }}, 
                               {{ $lemma->features && $lemma->features->impersonal ? 1 : 0 }} )" 
                       title="Изменить лемму"></i>
                    <i class="fa fa-plus fa-lg clickable link-color" 
                       onClick="addMeaning({{ $lemma->id }}, {{ $label_id }})"
                       title="Добавить новое значение"></i>
                    <a class="set-status status{{$lemma->labelStatus($label_id)}}" 
                       id="status-{{ $lemma->id }}" 
                       title="{{$lemma->labelStatus($label_id) ? 'снять пометку' : 'пометить как проверенное'}}"
                       onClick="setStatus({{ $lemma->id }}, {{ $label_id }})"
                       data-old="{{$lemma->labelStatus($label_id)}}" 
                       data-new="{{$lemma->labelStatus($label_id) ? 0 : 1}}"></a>
                    <i class="fa fa-trash fa-lg remove-label" onClick="removeLabel({{ $lemma->id }}, {{ $label_id }})" title="Удалить из списка"></i>
                </td>
            </tr>
