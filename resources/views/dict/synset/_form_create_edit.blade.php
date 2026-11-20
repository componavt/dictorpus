<input type="hidden" name="synset_form" value="1">
@if ($restore_members)
    <input type="hidden" id="synset-restored-dominant" value="{{ $form_dominant_id }}">
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif

@include('widgets.form._url_args_by_post',['url_args'=>$url_args])
    @if (!empty($synset) && $action=='edit' && !$restore_members)
    <table style="width: 100%">
            @if (count($synset->core))
        <tr><th colspan='3'>{{ trans('dict.core') }}</th><th>{{ trans('dict.syntype') }}</th></tr>
                @foreach ($core_members as $meaning)
@include('/dict/synset/_meaning_row', ['syntype_id'=>$meaning->pivot->syntype_id, 'button'=>'remove', 'disabled'=>false])
                @endforeach
            @endif
        
            @if (count($synset->periphery))
        <tr><th colspan='3'>{{ trans('dict.periphery') }}</th><th>{{ trans('dict.syntype') }}</th></tr>
                @foreach ($periphery_members as $meaning)
@include('/dict/synset/_meaning_row', ['syntype_id'=>$meaning->pivot->syntype_id, 'button'=>'remove', 'disabled'=>false])
                @endforeach
            @endif                
    </table>
        @endif
    @include('dict.synset._text_fields')

    @include('widgets.form.formitem._textarea',
            ['name' => 'comment',
                'attributes' => ['rows' => 3],
                'title' => 'Русские синонимы'])                

    <table style="width: 100%">
        <tr><th><i class="fa fa-sync-alt fa-lg reload-list" title="перегрузить список с учетом комментария" onclick="reloadPotentialMembers('{{ !empty($synset)? $synset->id : 0 }}', '{{ !empty($synset)? $synset->lang_id : $lang->id }}')"></i></th>
            <th colspan='2' style='padding: 0 20px'>{{ trans('dict.potential_members') }}</th>
            <th>{{ trans('dict.syntype') }}</th></tr>
        <tbody id='potential-members'>
        @if ($action=='edit' && count($potential_members))
@include('/dict/synset/_potential_rows')
        @endif
        </tbody>
        <tbody id="new-members">
        @if ($restore_members)
            @if (count($restored_members))
                <tr><th colspan="3">{{ trans('synset_form.selected_members') }}</th><th>{{ trans('dict.syntype') }}</th></tr>
            @endif
            @foreach ($restored_members as $meaning)
                @include('dict.synset._meaning_row', [
                    'syntype_id' => $restored_member_types[$meaning->id],
                    'button' => 'remove',
                    'disabled' => false,
                ])
            @endforeach
        @endif
    </tbody>
    </table>
        
    <h3>Искать в словаре</h3>
    <p class="help-block">{{ trans('synset_form.members_save_hint') }}</p>
    <div class="row">
        <div class='col-sm-9'>
    @include('widgets.form.formitem._select2', [
        'name' => 'new_members',
        'class' => 'select-member',
        'values' => $new_member_values ?? [],
        'value' => array_keys($new_member_values ?? []),
    ])
        </div>
        <div class='col-sm-3'>
            <input class="btn btn-info btn-default" type="button" value="добавить" onclick="addMember()">            
        </div>
    </div>

    <div class="form-group">
        <button type="submit" name="after_save" value="edit" class="btn btn-primary btn-default">
            {{ $submit_title }}
        </button>

        <button type="submit" name="after_save" value="list" class="btn btn-default">
            {{ trans($action === 'create' ? 'messages.create_and_back_to_list' : 'messages.save_and_back_to_list') }}
        </button>
    </div>
