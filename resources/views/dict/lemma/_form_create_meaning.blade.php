            <h3>@include('widgets.form._formitem_text',
                       ['name' => 'new_meanings['.$count.'][meaning_n]',
                        'value'=> $new_meaning_n,
                        'attributes'=>['size' => 2],
                        'tail' => trans('dict.meaning')])</h3>
            <table class="table-interpretations-translations">
                <tr>
                    <th>{{ trans('dict.lang') }}</th>
                    <th>{{ trans('dict.interpretation') }}</th>
                    <th></th>
                </tr>
            @foreach ($langs_for_meaning as $lang_id => $lang_text)
                <tr>
                    <td>{{ $lang_text }}&nbsp; </td>
                    <td>@include('widgets.form._formitem_text',
                       ['name' => 'new_meanings['.$count.'][meaning_text]['.$lang_id.']'])</td>
                    <td></td>
                </tr>
            @endforeach
            </table>
