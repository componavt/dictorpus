@include('widgets.form._url_args_by_post',['url_args'=>$url_args])
    <table style="width: 100%">
        @if (!empty($synset) && $action=='edit')
            @if (count($synset->core))
        <tr><th colspan='3'>{{ trans('dict.core') }}</th><th>{{ trans('dict.syntype') }}</th></tr>
                @foreach ($synset->coreWithFrequencies() as $meaning)
@include('/dict/synset/_meaning_row', ['syntype_id'=>$meaning->pivot->syntype_id, 'button'=>'remove', 'disabled'=>false])
                @endforeach
            @endif
        
            @if (count($synset->periphery))
        <tr><th colspan='3'>{{ trans('dict.periphery') }}</th><th>{{ trans('dict.syntype') }}</th></tr>
                @foreach ($synset->periphery as $meaning)
@include('/dict/synset/_meaning_row', ['syntype_id'=>$meaning->pivot->syntype_id, 'button'=>'remove', 'disabled'=>false])
                @endforeach
            @endif                
    </table>
        @endif

    <div class="row">
        <div class="col-sm-6">
            @include('widgets.form.formitem._textarea',
                    ['name' => 'comment',
                     'attributes' => ['rows' => 3],
                     'title' => 'Русские синонимы'])                
        </div>
        <div class="col-sm-6">
            @include('widgets.form.formitem._textarea',
                    ['name' => 'descr',
                     'attributes' => ['rows' => 3],
                     'title' => 'Определение доминанты'])                
        </div>
    </div>

    <table style="width: 100%">
        <tr><th><i class="fa fa-sync-alt fa-lg reload-list" title="перегрузить список с учетом комментария" onclick="reloadPotentialMembers('{{ !empty($synset)? $synset->id : 0 }}', '{{ !empty($synset)? $synset->lang_id : $lang->id }}')"></i></th>
            <th colspan='2' style='padding: 0 20px'>{{ trans('dict.potential_members') }}</th>
            <th>{{ trans('dict.syntype') }}</th></tr>
        <tbody id='potential-members'>
        @if ($action=='edit' && count($potential_members))
@include('/dict/synset/_potential_rows')
        @endif
        </tbody>
        <tbody id='new-members'>
        </tbody>
    </table>
        
    <h3>Искать в словаре</h3>
    <div class="row">
        <div class='col-sm-9'>
    @include('widgets.form.formitem._select2',
            ['name' => 'new_members',
             'class'=>'select-member'])
        </div>
        <div class='col-sm-3'>
            <input class="btn btn-info btn-default" type="button" value="добавить" onclick="addMember()">            
        </div>
    </div>

    @include('widgets.form.formitem._submit', ['title' => $submit_title])
