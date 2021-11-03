<?php
    $event_informants = \DB::table('event_informant')
                          ->where('event_id', $event->id)->get(['informant_id']);
    $informants_arr = [];
    foreach ($event_informants as $event_informant) {
        $informant = \App\Models\Corpus\Informant::find($event_informant->informant_id);
        if ($informant) {
            $informants_arr[] = $informant->informantString();
        }
    }
    $informant_list = join("<br>\n",$informants_arr);

    $event_recorders = \DB::table('event_recorder')
                          ->where('event_id', $event->id)->get(['recorder_id']);
    $recorders_arr = [];
    foreach ($event_recorders as $event_recorder) {
            $recorders_arr[] = \App\Models\Corpus\Recorder::find($event_recorder->recorder_id)->name;
    }
    $recoders_list = join(', ',$recorders_arr);
?>
@if ($informants_arr)
        <i>{{ trans('corpus.informants')}}:</i> 
        @foreach ($informants_arr as $informant) 
            {{$informant}}<br>
        @endforeach
@endif

@if ($event->place)
    <i>{{ trans('corpus.record_place')}}:</i> @include('corpus.place._to_string',
                                                       ['place' => $event->place, 'lang_id' => $lang_id])@if($event->date 
        || $event->recorders),@endif
@endif

@if ($event->date)
<i>{{ trans('corpus.record_year')}}:</i> {{ $event->date }}@if($recoders_list)<br>@endif
@endif

@if ($recoders_list)
    <i>{{ trans('corpus.recorded')}}:</i> {{ $recoders_list }}
@endif