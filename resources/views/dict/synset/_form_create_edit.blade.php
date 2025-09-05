    <table style="width: 100%">
        @if (count($synset->core))
        <tr><th colspan="2">{{ trans('dict.core') }}</th></tr>
            @foreach ($synset->core as $meaning)
<?php //dd($member['meaning']);    ?>
                @include('/dict/synset/_meaning_row', ['syntype_id'=>$meaning->pivot->syntype_id])
            @endforeach
        @endif
        
        @if (count($synset->periphery))
        <tr><th colspan="2">{{ trans('dict.periphery') }}</th></tr>
            @foreach ($synset->periphery as $meaning)
                @include('/dict/synset/_meaning_row', ['syntype_id'=>$meaning->pivot->syntype_id])
            @endforeach
        @endif
    </table>

    @include('widgets.form.formitem._textarea',
            ['name' => 'comment',
             'attributes' => ['rows' => 3],
             'title' => trans('corpus.comment')])
        
    @include('widgets.form.formitem._submit', ['title' => $submit_title])
