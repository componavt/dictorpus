        <tr id='meaning-{{ $meaning->id }}' class="meaning-row" style="@if ($meaning->id===$synset->dominant_id) font-weight:bold; @endif">
            <td>
                <span style="padding-right: 10px">
                    <input id="dominant-{{ $meaning->id }}" class="check-dominant" data-id="{{ $meaning->id }}" 
                           type='radio' name='dominant_id' value='{{ $meaning->id }}'{{ $meaning->id===$synset->dominant_id ? ' checked' : '' }}
                           style="@if ($disabled) display:none; @endif" title="Выберите доминанту">
                </span>
            </td>         
            <td id='meaning_td_{{ $meaning->id }}' style='padding-right:10px'>
@include('dict.synset._button_'.$button)                
            </td>
            <td class="meanings" data-id="{{ $meaning->id }}" style='padding-right: 20px'><a href="{{ route('lemma.show', $meaning->lemma->id) }}">{{ $meaning->lemma->lemma }}</a>: 
                {{ $meaning->meaning_n}}. {{ $meaning->getMeaningTextLocale() }} 
                (<span class="warning">{{ isset($meaning->freq) ? $meaning->freq : $meaning->textFrequency() }}</span>{!! 
                !empty($meaning->countBestExamples()) ? ' / <span class="relevance-10">'.$meaning->countBestExamples().'</span>' : '' !!}{!! 
                !empty($meaning->countGreatExamples()) ? ' / <span class="relevance-7">'.$meaning->countGreatExamples().'</span>' : '' !!})</td>
            <td>
                @include('widgets.form.formitem._select',
                        ['name' => 'meanings['.$meaning->id.'][syntype_id]',
                         'values' =>$syntype_values,
                         'value' => $syntype_id,
                         'attributes' => ['disabled' => $disabled]])
            </td>
        </tr>
