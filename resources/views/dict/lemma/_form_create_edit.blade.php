<div class="row">
    <div class="col-sm-6">
        @include('widgets.form._formitem_text', ['name' => 'lemma', 'title'=>trans('dict.lemma')])
        @include('widgets.form._formitem_select',
                ['name' => 'lang_id',
                 'values' =>$lang_values,
                 'title' => trans('dict.lang')])
        @include('widgets.form._formitem_select',
                ['name' => 'pos_id',
                 'values' =>$pos_values,
                 'title' => trans('dict.pos')])
        @if ($action == 'edit')
            @foreach ($lemma->meanings as $meaning)
            <div>
                <h3>@include('widgets.form._formitem_text',
                           ['name' => 'ex_meanings['.$meaning->id.'][meaning_n]',
                            'value'=> $meaning->meaning_n,
                            'attributes'=>['size' => 2],
                            'tail' => trans('dict.meaning')])</h3>
                <table>
                    <tr>
                        <th>{{ trans('dict.lang') }}</th>
                        <th>{{ trans('dict.interpretation') }}</th>
                    </tr>
                @foreach ($meaning->meaningTextsWithAllLangs() as $meaning_lang => $meaning_text)
                    <tr>
                        <td>{{ $meaning_text->lang_name}}</td>
                        <td>@include('widgets.form._formitem_text',
                           ['name' => 'ex_meanings['.$meaning->id.'][meaning_text]['.$meaning_lang.']',
                            'value'=> $meaning_text->meaning_text])</td>
                    </tr>
                @endforeach
                </table>

                @foreach ($relation_values as $relation_id => $relation_text)
                    <?php $relation_value = isset($relation_meanings[$meaning->id][$relation_id]) ? $relation_meanings[$meaning->id][$relation_id] : array(); ?>
                    @include('widgets.form._formitem_select2',
                            ['name' => 'ex_meanings['.$meaning->id.'][relation]['.$relation_id.']',
                             'title' => $relation_text,
                             'values' => $all_meanings,
                             'value' => $relation_value,
                             'attributes'=>['multiple'=>'multiple']
                        ])
                @endforeach
            </div>
            @endforeach
        @endif

        {{-- New meaning --}}
        <div>
            <h3>@include('widgets.form._formitem_text',
                       ['name' => 'new_meanings[0][meaning_n]',
                        'value'=> $new_meaning_n,
                        'attributes'=>['size' => 2],
                        'tail' => trans('dict.meaning')])</h3>
            <table>
                <tr>
                    <th>{{ trans('dict.lang') }}</th>
                    <th>{{ trans('dict.interpretation') }}</th>
                </tr>
            @foreach ($langs_for_meaning as $lang_id => $lang_text)
                <tr>
                    <td>{{ $lang_text }}&nbsp; </td>
                    <td>@include('widgets.form._formitem_text',
                       ['name' => 'new_meanings[0][meaning_text]['.$lang_id.']'])</td>
                </tr>
            @endforeach
            </table>
        </div>

        @include('widgets.form._formitem_btn_submit', ['title' => $submit_title])
    </div>
    <div class="col-sm-6">
        <p><b>{{ trans('dict.wordforms') }}</b></p>
        @if ($action == 'edit')
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
        @endif
    </div>
</div>


