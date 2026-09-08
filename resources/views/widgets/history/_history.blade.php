    <h3>{{ trans('messages.history') }}</h3>
        @foreach($all_history as $time => $histories )
<?php 
$dt = \Carbon\Carbon::parse($time);
//dd($all_history);
$user = $histories[0]->userResponsible() ? \App\Models\User::find($histories[0]->userResponsible()->id) : null; 
$histories = $histories->sortBy('id');
$history_strings = [];

$diffConfig = new Caxy\HtmlDiff\HtmlDiffConfig();
$diffConfig->setPurifierCacheLocation(
    storage_path('app/htmlpurifier')
);

foreach($histories as $history) {
    $fieldName = $history->fieldName();
    if (!isset($history->field_name)) {
        $history->field_name = trans('history.'.$fieldName.'_accusative');
    }    
    
    if ($fieldName == 'created_at') :
        if (isset($history->what_created)):
            $history_strings[] = trans('messages.created'). ' '
                               . $history->what_created;
        endif;
    elseif ($history->oldValue() == null) : //preg_match("/MeaningText$/",$history->revisionable_type) && 
            $history_strings[] = trans('messages.created'). ' '
                               . $history->field_name .': <b>'
                               . $history->newValue().'</b>';
    elseif (!empty($history->oldValue()) && empty($history->newValue())) : 
            $history_strings[] = trans('messages.deleted'). ' '
                               . $history->field_name .'</b>';
    elseif ($fieldName == 'text') :
//            $diff = \Diff::compare($history->oldValue(), $history->newValue());
            $htmlDiff = HtmlDiff::create($history->oldValue(), $history->newValue(),$diffConfig);
            $history_strings[] = trans('messages.changed'). ' '
//                               . $history->field_name. '<br>'.$diff->toHTML();
                               . $history->field_name. '<br>'.$htmlDiff->build();
    else :
            $history_strings[] = trans('messages.changed'). ' '
                               . $history->field_name. '<br>'
                               . trans('messages.from'). ' ' 
                               . ' <span class="old-value">'. $history->oldValue(). '</span><br>' 
                               . trans('messages.to'). ' '
                               . '<span class="new-value">'. $history->newValue(). '</span>';
    endif;
}
?>
            @if (sizeof($history_strings))
        <p>
            <span class="date">{{ $dt->formatLocalized(trans('main.datetime_format')) }}</span>
            {{ $user ? $user->name : '' }} 
            <ul>
            @foreach($history_strings as $history)
                <li>{!!$history!!}</li>
            @endforeach
            </ul>
        </p>
            @endif
        @endforeach
        