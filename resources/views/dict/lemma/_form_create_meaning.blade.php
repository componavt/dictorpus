        @if (isset($new_meaning_n))
            <h3>@include('widgets.form.formitem._text',
                       ['name' => 'new_meanings['.$count.'][meaning_n]',
                        'value'=> $new_meaning_n,
                        'attributes'=>['size' => 2],
                        'tail' => trans('dict.meaning')])</h3>
        @endif
            <table class="table-interpretations">
                <tr>
                    <th>{{ trans('dict.lang') }}</th>
                    <th>{{ trans('dict.interpretation') }}</th>
                    <th></th>
                </tr>
            @foreach ($langs_for_meaning as $lang_id => $lang_text)
                <tr>
                    <td>{{ $lang_text }}&nbsp; </td>
                    <td>@include('widgets.form.formitem._text',
                       ['name' => 'new_meanings['.$count.'][meaning_text]['.$lang_id.']',
                        'special_symbol' => true,
                       ])</td>
                    <td></td>
                </tr>
            @endforeach
            </table>
