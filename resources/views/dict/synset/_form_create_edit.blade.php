    <table style="width: 100%">
        @if (count($synset->core))
        <tr><th colspan='2'>{{ trans('dict.core') }}</th><th>{{ trans('dict.syntype') }}</th></tr>
            @foreach ($synset->core as $meaning)
<?php //dd($member['meaning']);    ?>
                @include('/dict/synset/_meaning_row', ['syntype_id'=>$meaning->pivot->syntype_id])
            @endforeach
        @endif
        
        @if (count($synset->periphery))
        <tr><th colspan='2'>{{ trans('dict.periphery') }}</th><th>{{ trans('dict.syntype') }}</th></tr>
            @foreach ($synset->periphery as $meaning)
                @include('/dict/synset/_meaning_row', ['syntype_id'=>$meaning->pivot->syntype_id])
            @endforeach
        @endif                
    </table>

    @include('widgets.form.formitem._textarea',
            ['name' => 'comment',
             'attributes' => ['rows' => 3],
             'title' => trans('corpus.comment')])
        
        @if ($action=='edit' && count($potential_members))
    <table style="width: 100%">
        <tr><th><i class="fa fa-sync-alt fa-lg reload-list" title="перегрузить список с учетом комментария" onclick="reloadPotentialMembers({{ $synset->id }})"></i></th>
            <th style='padding: 0 20px'>{{ trans('dict.potential_members') }}</th>
            <th>{{ trans('dict.syntype') }}</th></tr>
        <tbody id='potential-members'>
            @include('/dict/synset/_potential_rows')
        </tbody>
    </table>
        @endif
        
    @include('widgets.form.formitem._submit', ['title' => $submit_title])
