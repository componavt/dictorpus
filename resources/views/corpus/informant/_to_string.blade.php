<?php
        $info = [];
        
        if ($informant->name) {
            $info[] = '<i>'. $informant->name. '</i>';
        }
        
        if ($informant->birth_date) {
            $info[] = '<b>'.\Lang::get('corpus.birth_year'). '</b> <i>'. $informant->birth_date.'</i>';
        }

        $informant_info = join(', ', $info);
?>
@if ($informant_info)
<b>{{ trans('corpus.informant')}}:</b> 
{!! $informant_info !!},

    @if ($informant->birth_place)
    <b>{{ trans('corpus.nee')}}</b> 
    <i>@include('corpus.place._to_string',['place' => $informant->birth_place, 'lang_id' => $lang_id])@endif</i>    
@endif