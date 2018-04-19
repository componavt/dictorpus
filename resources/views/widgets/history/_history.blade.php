        <h3>{{ trans('messages.history') }}</h3>
        @foreach($lemma->allHistory() as $time => $histories )
<?php 
$user = \App\Models\User::find($histories[0]->userResponsible()->id); 
$histories = $histories->sortBy('id');
$history_strings = [];
foreach($histories as $history) {
    $fieldName = $history->fieldName();
    if (!isset($history->field_name)) {
        $history->field_name = trans('history.'.$fieldName.'_accusative');
    }    
    
    if ($fieldName == 'created_at') :
        if (isset($history->model_accusative)):
            $history_strings[] = trans('messages.created'). ' '
                               . $history->model_accusative;
        endif;
    elseif (preg_match("/MeaningText$/",$history->revisionable_type) && !$history->oldValue()) :
            $history_strings[] = trans('messages.created'). ' '
                               . $history->field_name .': <b>'
                               . $history->newValue().'</b>';
    elseif (!($fieldName=='reflexive' && $history->oldValue() == null) ) :
            $history_strings[] = trans('messages.changed'). ' '
                               . $history->field_name. ' '
                               . trans('messages.from'). ' ' 
                               . ' <span class="old-value">'. $history->oldValue(). '</span> ' 
                               . trans('messages.to'). ' '
                               . '<span class="new-value">'. $history->newValue(). '</span>';
    endif;
}
?>
            @if (sizeof($history_strings))
        <p>
            <i>{{ $time }}</i>
            {{ $user->name }} 
            <ul>
            @foreach($history_strings as $history)
                <li>{!!$history!!}</li>
            @endforeach
            </ul>
        </p>
            @endif
        @endforeach
        