        @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
            <table>
                <tr>
                    <th>&nbsp;</th>
                    @foreach (array_values($dialects) as $dialect_name)
                    <th>{{$dialect_name}}</th>
                    @endforeach
                </tr>
                @if ($lemma->wordformsWithAllGramsets())
                    @foreach ($lemma->wordformsWithAllGramsets() as $gramset_id => $dialect_wordform)
                    <tr>
                        <td>
                            {{\App\Models\Dict\Gramset::find($gramset_id)->gramsetString(', ',true)}}&nbsp;
                        </td>
                        @foreach ($dialect_wordform as $dialect_id => $wordform)
                        <?php $wordform_value = ($wordform) ? ($wordform->wordform) : NULL; ?>
                        <td>@include('widgets.form._formitem_text',
                               ['name' => 'lang_wordforms['.$gramset_id.']['.$dialect_id.']',
                                'value'=> $wordform_value])</td>
                        @endforeach
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
                            'value'=> $wordform->wordform])</td>
               </tr>
                @endforeach
            </table>

