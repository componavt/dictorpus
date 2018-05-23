<?php
/*$options = [
    // Compare by line or by characters
    'compareCharacters' => false,
    // Offset size in hunk groups
    'offset'            => 2,
];

$diff = \Diff::compare("hello\na", "hello\nasd\na", $options);
$groups = $diff->getGroups();

foreach($groups as $i => $group)
{
    // Output: Hunk 1 : Lines 2 - 6
    echo 'Hunk ' . $i . ' : Lines ' 
         . $group->getFirstPosition() . ' - ' . $group->getLastPosition(); 
    
    // Output changed lines (entries)
    foreach($group->getEntries() as $entry)
    {
        // Output old position of line
        echo $entry instanceof \ViKon\Diff\Entry\InsertedEntry 
            ? '-'
            : $entry->getOldPosition() + 1;

        echo ' | ';

        // Output new position of line
        echo $entry instanceof \ViKon\Diff\Entry\DeletedEntry 
            ? '-'
            : $entry->getNewPosition() + 1;
        
        echo ' - ';        

        // Output line (entry)
        echo $entry;
    }
}*/
?>
    <h3>{{ trans('messages.history') }}</h3>
        @foreach($all_history as $time => $histories )
<?php 
$dt = \Carbon\Carbon::parse($time);
//dd($all_history);
$user = \App\Models\User::find($histories[0]->userResponsible()->id); 
$histories = $histories->sortBy('id');
$history_strings = [];
$diffConfig = new Caxy\HtmlDiff\HtmlDiffConfig();
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
            {{ $user->name }} 
            <ul>
            @foreach($history_strings as $history)
                <li>{!!$history!!}</li>
            @endforeach
            </ul>
        </p>
            @endif
        @endforeach
        