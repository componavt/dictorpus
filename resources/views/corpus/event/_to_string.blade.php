<?php
    $informants_arr = $event->informantsWithLink('/corpus/text?'.($text->getCollectionId() ? 'search_collection_id='.$text->getCollectionId().'&' : '').'search_');
    $recorders_arr = $event->recordersWithLink('/corpus/text?search_recorder=');
    $recoders_list = join(', ',$recorders_arr);
?>
@if ($informants_arr)
        <div class="metadata-title">{{ trans('corpus.informants')}}:</div> 
        <i>
        @foreach ($informants_arr as $informant) 
            {!! $informant !!}<br>
        @endforeach
        </i>
@endif

@if ($event->place)
    <b>{{ trans('corpus.record_place')}}:</b> 
    <i>{!! $event->place->placeString('', true, '/corpus/text?search_event_place='); !!}@if($event->date 
        || $event->recorders),@endif
    </i>
@endif

@if ($event->date)
<b>{{ trans('corpus.record_year')}}:</b> 
<i>{!! to_link($event->date, '/corpus/text?search_year_from='.$event->date.'&search_year_to='.$event->date) !!}@if($recoders_list)<br>@endif</i>
@endif

@if ($recoders_list)
<b>{{ trans('corpus.recorded')}}:</b> <i>{!! $recoders_list !!}</i>
@endif