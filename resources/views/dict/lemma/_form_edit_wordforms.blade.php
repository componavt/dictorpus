        @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
            <table>
                <tr>
                    <th>&nbsp;</th>
                    <th>{{$dialect_name}}</th>
                </tr>
                @if ($lemma->wordformsWithAllGramsets($dialect_id))
                    @foreach ($lemma->wordformsWithAllGramsets($dialect_id) as $gramset_id => $dialect_wordform)
                    <tr>
                        <td>
                            {{\App\Models\Dict\Gramset::find($gramset_id)->gramsetString(', ',true)}}&nbsp;
                        </td>
                        <?php $wordform_value = ($dialect_wordform[$dialect_id]) ? ($dialect_wordform[$dialect_id]->wordform) : NULL; ?>
                        <td>@include('widgets.form._formitem_text',
                               ['name' => 'lang_wordforms['.$gramset_id.']['.$dialect_id.']',
                                'special_symbol' => true,
                                'value'=> $wordform_value])</td>
                   </tr>
                    @endforeach
                @endif
                @foreach ($lemma->wordformsWithoutGramsets() as $key=>$wordform)
                <tr>
                    <td>
                        @include('widgets.form._formitem_select', 
                                ['name' => 'empty_wordforms['.$key.'][gramset]', 
                                 'values' =>$gramset_values]) 
                    </td>
                    <td>@include('widgets.form._formitem_text', 
                           ['name' => 'empty_wordforms['.$key.'][wordform]', 
                            'special_symbol' => true,
                            'value'=> $wordform->wordform])</td>
               </tr>
                @endforeach
            </table>

