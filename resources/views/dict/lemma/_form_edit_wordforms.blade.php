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
                        @foreach ([0,1] as $i)
                        <td>@include('widgets.form._formitem_text',
                               ['name' => 'lang_wordforms['.$gramset_id.']['.$dialect_id.']['.$i.']',
                                'special_symbol' => true,
                                'value'=> isset($dialect_wordform[$dialect_id][$i]) ? $dialect_wordform[$dialect_id][$i]->wordform : NULL
                               ])</td>
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
                            'special_symbol' => true,
                            'value'=> $wordform->wordform])</td>
                    <td>
                        @include('widgets.form._formitem_select', 
                                ['name' => 'empty_wordforms['.$key.'][dialect]', 
                                 'values' =>$dialect_values]) 
                    </td>
               </tr>
                @endforeach
            </table>

