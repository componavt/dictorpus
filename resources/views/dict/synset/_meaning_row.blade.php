        <tr id='meaning-{{ $meaning->id }}'>
            <td><i class="fa fa-trash fa-lg remove-from-list" onClick="removeMeaningFromList({{ $meaning->id }})" title="Удалить из синсета"></i>&nbsp;</td>
            <td class="meanings" data-id="{{ $meaning->id }}" style='padding-right: 20px'><a href="{{ route('lemma.show', $meaning->lemma->id) }}">{{ $meaning->lemma->lemma }}</a>: 
                {{ $meaning->meaning_n}}. {{ $meaning->getMeaningTextLocale() }} </td>
            <td>
                @include('widgets.form.formitem._select',
                        ['name' => 'meanings['.$meaning->id.'][syntype_id]',
                         'values' =>$syntype_values,
                         'value' => $syntype_id])
            </td>
        </tr>
