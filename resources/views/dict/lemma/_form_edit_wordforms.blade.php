@include('widgets.form._url_args_by_post',['url_args'=>$url_args])
        <div class="table-responsive">
            <table class="word-form-table">
                <tr>
                    <th>&nbsp;</th>
                    <th class="warning">{{trans('dict.wordform_field_comments')}}</th>
                    <th>{{$dialect_name}}</th>
                </tr>
                @if ($lemma->wordformsWithAllGramsets($dialect_id))
                    @foreach ($lemma->wordformsWithAllGramsets($dialect_id) as $category_name => $category_gramsets)
                    <tr>
                        <td colspan="3"><b><big>{{$category_name}}</big></b></td>
                    </tr>
                        @foreach ($category_gramsets as $gramset_id => $dialect_wordform)
                    <tr>
                        <td>
                            {{\App\Models\Dict\Gramset::find($gramset_id)->gramsetString(', ',true)}}&nbsp;
                        </td>
                        <td>@include('widgets.form.formitem._text',
                               ['name' => 'lang_wordforms['.$gramset_id.']['.$dialect_id.']',
                                'special_symbol' => true,
                                'value'=> $lemma->wordform($gramset_id,$dialect_id)
                               ])</td>
                        <td>@include('widgets.form.formitem._select', 
                                ['name' => 'lang_wordforms_dialect['.$gramset_id.']', 
                                 'values' =>$dialect_values,
                                 'value' =>$dialect_id]) 
                        </td>
                   </tr>
                        @endforeach
                    @endforeach
                @endif
                @foreach ($lemma->wordformsWithoutGramsets() as $key=>$wordform)
                <tr>
                    <td>
                        @include('widgets.form.formitem._select', 
                                ['name' => 'empty_wordforms['.$key.'][gramset]', 
                                 'values' =>$gramset_values]) 
                    </td>
                    <td>@include('widgets.form.formitem._text', 
                           ['name' => 'empty_wordforms['.$key.'][wordform]', 
                            'special_symbol' => true,
                            'value'=> $wordform->wordform])</td>
                    <td>
                        @include('widgets.form.formitem._select', 
                                ['name' => 'empty_wordforms['.$key.'][dialect]', 
                                 'values' =>$dialect_values]) 
                    </td>
               </tr>
                @endforeach
            </table>
        </div>

