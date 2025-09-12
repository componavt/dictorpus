        <tr id='meaning-{{ $meaning->id }}'>
            <td id='meaning_td_{{ $meaning->id }}'><i class="fa fa-plus-circle fa-lg add-to-list" onClick="addMeaningToList({{ $meaning->id }})" title="Добавить в синсет"></i>&nbsp;</td>
            <td style='padding: 0 20px'><a href="{{ route('lemma.show', $meaning->lemma->id) }}">{{ $meaning->lemma->lemma }}</a>: 
                {{ $meaning->meaning_n}}. {{ $meaning->getMeaningTextLocale() }} </td>
            <td>
                @include('widgets.form.formitem._select',
                        ['name' => 'meanings['.$meaning->id.'][syntype_id]',
                         'values' =>$syntype_values,
                         'value' => $syntype_id,
                         'attributes' => ['disabled' => '1']])
            </td>
        </tr>
