<?php
/*    $event_informants = \DB::table('event_informant')
                          ->where('event_id', $event->id)->get(['informant_id']);
    $informants_arr = [];
    foreach ($event_informants as $event_informant) {
        $informant = \App\Models\Corpus\Informant::find($event_informant->informant_id);
        if ($informant) {
            $informants_arr[] = $informant->informantString();
        }
    }*/
    $informants_arr = $event->informantsWithLink('/corpus/text?search_informant=');
/*
    $event_recorders = \DB::table('event_recorder')
                          ->where('event_id', $event->id)->get(['recorder_id']);
    $recorders_arr = [];
    foreach ($event_recorders as $event_recorder) {
            $recorders_arr[] = \App\Models\Corpus\Recorder::find($event_recorder->recorder_id)->name;
    }
    */
    $recorders_arr = $event->recordersWithLink('/corpus/text?search_recorder=');
    $recoders_list = join(', ',$recorders_arr);
?>
@if ($informants_arr)
        <div class="metadata-title">{{ trans('corpus.informants')}}:</div> 
        <i>
        @foreach ($informants_arr as $informant) 
            {!!$informant!!}<br>
        @endforeach
        </i>
@endif

@if ($event->place)
    <b>{{ trans('corpus.record_place')}}:</b> 
    <i>{!! $event->placeWithLink('/corpus/text?search_place='); !!}@if($event->date 
        || $event->recorders),@endif
    </i>
@endif

@if ($event->date)
<b>{{ trans('corpus.record_year')}}:</b> <i>{{ $event->date }}@if($recoders_list)<br>@endif</i>
@endif

@if ($recoders_list)
<b>{{ trans('corpus.recorded')}}:</b> <i>{!! $recoders_list !!}</i>
@endif