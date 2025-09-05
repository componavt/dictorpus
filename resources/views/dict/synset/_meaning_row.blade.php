        <tr>
            <td><i class="fa fa-trash fa-lg remove-meaning" onClick="removeMeaning({{ $meaning->id }})" title="Удалить из синсета"></i>&nbsp;</td>
            <td style='padding-right: 20px'><a href="{{ route('lemma.show', $meaning->lemma->id) }}">{{ $meaning->lemma->lemma }}</a>: 
                {{ $meaning->meaning_n}}. {{ $meaning->getMeaningTextLocale() }} </td>
            <td>
                @include('widgets.form.formitem._select',
                        ['name' => 'syntypes['.$meaning->id.']',
                         'values' =>$syntype_values,
                         'value' => $syntype_id,
                         'title' => trans('dict.syntype')])
            </td>
        </tr>
