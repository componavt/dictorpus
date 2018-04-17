<?php //dd($history);
//    $user = \App\Models\User::find($history->userResponsible()->id);
    $fieldName = $history->fieldName()?>
@if ($fieldName != 'updated_at')
            <li>
                @if ($fieldName == 'created_at')
                {{trans('messages.created')}} {{$history->revisionable_type}}
                @else
                {{trans('messages.changed')}} 
                {{ trans('history.'.$fieldName) }} 
                {{trans('messages.from')}} 
                <b>{{ $history->oldValue() }}</b> 
                {{trans('messages.to')}} 
                <b>{{ $history->newValue() }}</b>
                @endif
            </li>
@endif                
        