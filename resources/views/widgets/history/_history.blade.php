        <h3>{{ trans('messages.history') }}</h3>
        @foreach($lemma->allHistory() as $time => $histories )
        <?php $user = \App\Models\User::find($histories[0]->userResponsible()->id); ?>
        <p>
            <i>{{ $time }}</i>
            {{ $user->name }} 
            <ul>
            <?php $histories1 = $histories->sortBy('id');?>
            @foreach($histories1 as $history)
<?php 
/*$s = preg_quote("\\");
if (preg_match("/".$s."([^".$s."]+)$/",$history->revisionable_type, $regs)) {
    $model_accusative = trans('history.'.strtolower($regs[1]).'_accusative');
} else $model_accusative = '';*/
$fieldName = $history->fieldName();
if (!isset($history->field_name)) {
    $history->field_name = trans('history.'.$fieldName.'_accusative');
}
?>
                @if ($fieldName == 'created_at') 
                    @if (isset($history->model_accusative))
                    <li>
                        {{trans('messages.created')}} {{$history->model_accusative}}
                    </li>
                    @endif
                @elseif (preg_match("/MeaningText$/",$history->revisionable_type) && !$history->oldValue())
                    <li>
                        {{trans('messages.created')}} {{ $history->field_name }}: <b>{{ $history->newValue() }}</b>
                    </li>
                @elseif (!($fieldName=='reflexive' && $history->oldValue()) != null )
                    <li>
                        {{trans('messages.changed')}} 
                        {{$history->field_name}} 
                        {{trans('messages.from')}} 
                        <span class="old-value">{{ $history->oldValue() }}</span> 
                        {{trans('messages.to')}} 
                        <span class="new-value">{{ $history->newValue() }}</span>
                    </li>
                @endif
            @endforeach
            </ul>
        </p>
        @endforeach
        