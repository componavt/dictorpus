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
                 'title' => trans('dict.pos'),
                 'placeholder' => trans('dict.select_pos')]) 
        @if ($action == 'edit')
            @foreach ($lemma->meanings as $meaning)
            <div>
                <h3>@include('widgets.form._formitem_text', 
                           ['name' => 'meaning['.$meaning->id.'][meaning_n]', 
                            'value'=> $meaning->meaning_n,
                            'size' => 2,
                            'tail' => trans('dict.meaning')])</h3>
                <table>
                    <tr>
                        <th>{{ trans('dict.lang') }}</th>
                        <th>{{ trans('dict.interpretation') }}</th>
                    </tr>
                @foreach ($meaning->meaningTexts as $meaning_text)
                    <tr>
                        <td>@include('widgets.form._formitem_select', 
                           ['name' => 'meaning_text['.$meaning_text->id.'][lang_id]', 
                            'values' =>$lang_values,
                            'value'=> $meaning_text->lang_id])</td> 
                        <td>@include('widgets.form._formitem_text', 
                           ['name' => 'meaning_text['.$meaning_text->id.'][meaning_text]', 
                            'value'=> $meaning_text->meaning_text])</td>
                    </tr>
                @endforeach
                </table>
            </div>
            @endforeach
        @endif
        @include('widgets.form._formitem_btn_submit', ['title' => $submit_title])
    </div>
    <div class="col-sm-6">
        <p><b>{{ trans('dict.wordforms') }}</b></p>
        @if ($action == 'edit')
            @if ($lemma->wordforms()->count())
            <table>
                @foreach ($lemma->wordformsWithGramsets() as $wordform)
                <tr>
                    <td>@include('widgets.form._formitem_text', 
                           ['name' => 'wordform['.$wordform->id.'][wordform]', 
                            'value'=> $wordform->wordform])</td>
                    <td>
                        @include('widgets.form._formitem_select', 
                                ['name' => 'wordform['.$wordform->id.'][gramset]', 
                                 'values' =>$gramset_values,
                                 'value' => $wordform->gramset_id]) 
                    </td>
               </tr>
                @endforeach
            </table>
            @endif
        @endif
    </div>
</div>                 
                         


