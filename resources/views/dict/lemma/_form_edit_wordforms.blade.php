            <table>
                @if ($lemma->wordformsWithAllGramsets())
                    @foreach ($lemma->wordformsWithAllGramsets() as $gramset_id => $wordform)
                        <?php $wordform_value = ($wordform) ? ($wordform->wordform) : NULL; ?>
                    <tr>
                        <td>
                            {{\App\Models\Dict\Gramset::find($gramset_id)->gramsetString()}}&nbsp;
                        </td>
                        <td>@include('widgets.form._formitem_text',
                               ['name' => 'lang_wordforms['.$gramset_id.']',
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
                            'value'=> $wordform->wordform])</td>
               </tr>
                @endforeach
            </table>

