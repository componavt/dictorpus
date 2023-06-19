            <tr id="row-{{$lemma->id}}">
                <td data-th="No">{{ $list_count ?? '' }}</td>
                <td data-th="{{ trans('dict.lemma') }}">
                    <a href="{{ LaravelLocalization::localizeURL("/dict/lemma/".$lemma->id) }}">
                        {{$lemma->zaikovTemplate()}}
                    </a>
                </td>
                <td data-th="{{ trans('dict.pos') }}">
                        {{$lemma->pos ? $lemma->pos->dict_code : ''}}
                </td>
                <td data-th="{{ trans('dict.meanings') }}" id='meanings-{{ $lemma->id }}'>
                    @include('service.dict.lemma._meanings')
                </td>
                <td data-th="{{ trans('messages.actions') }}" style="text-align:center">
                    <i class="fa fa-plus fa-lg clickable link-color" onClick="addMeaning({{ $lemma->id }}, {{ $label_id }})"></i>
                    <a class="set-status status{{$lemma->labelStatus($label_id)}}" id="status-{{ $lemma->id }}" 
                       onClick="setStatus({{ $lemma->id }}, {{ $label_id }})"
                       data-old="{{$lemma->labelStatus($label_id)}}" 
                       data-new="{{$lemma->labelStatus($label_id) ? 0 : 1}}"></a>
                    <i class="fa fa-trash fa-lg remove-label" onClick="removeLabel({{ $lemma->id }}, {{ $label_id }})" title="Удалить из списка"></i>
                </td>
            </tr>
