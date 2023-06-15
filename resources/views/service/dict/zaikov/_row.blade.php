            <tr id="row-{{$lemma->id}}">
                <td data-th="No">{{ $list_count ?? '' }}</td>
                <td data-th="{{ trans('dict.lemma') }}">
                    <a href="{{ LaravelLocalization::localizeURL("/dict/lemma/".$lemma->id) }}">
                        {{$lemma->lemma}}
                    </a>
                </td>
                <td data-th="{{ trans('dict.pos') }}">
                        {{$lemma->pos->dict_code}}
                </td>
                <td data-th="{{ trans('dict.meanings') }}">
                @foreach ($lemma->meaningsWithLabel($label_id) as $meaning) 
                    <div id='meaning-{{$meaning->id}}'>
                        <i class="fa fa-times fa-lg clickable" data-delete="{{csrf_token()}}" onClick="removeLabelMeaning(this, {{$meaning->id}}, {{$label_id}}, '{{$meaning->getMultilangMeaningTextsString('ru')}}')"></i>
                        {{$meaning->getMultilangMeaningTextsString('ru')}}
                    @foreach ($meaning->examples as $example) 
                        @include('dict.example.view', ['meaning_id'=>$meaning->id, 'example_obj'=>$example])
                    @endforeach
                        <i id="add-example-for-{{$meaning->id}}" class="fa fa-plus fa-lg clickable link-color" onClick="addSimpleExample({{$meaning->id}})"></i>                        
                    </div>
                @endforeach
                </td>
                <td data-th="{{ trans('messages.actions') }}" style="text-align:center">
                    <a class="set-status status{{$lemma->labelStatus($label_id)}}" id="status-{{$lemma->id}}" 
                       onClick="setStatus({{$lemma->id}}, {{$label_id}})"
                       data-old="{{$lemma->labelStatus($label_id)}}" 
                       data-new="{{$lemma->labelStatus($label_id) ? 0 : 1}}"></a>
                    <i class="fa fa-trash fa-lg remove-label" onClick="removeLabel({{$lemma->id}}, {{$label_id}})" title="Удалить из списка"></i>
                </td>
            </tr>
