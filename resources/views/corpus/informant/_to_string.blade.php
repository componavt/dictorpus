<?php
        $info = [];
        
        if ($informant->name) {
            $info[] = $informant->name;
        }
        
        if ($informant->birth_date) {
            $info[] = '<i>'.\Lang::get('corpus.birth_year'). '</i> '. $informant->birth_date;
        }

        $informant_info = join(', ', $info);
?>
@if ($informant_info)
<i>{{ trans('corpus.informant')}}:</i> 
    {!! $informant_info !!},

    @if ($informant->birth_place)
    <i>{{ trans('corpus.nee')}}</i> @include('corpus.place._to_string',['place' => $informant->birth_place, 'lang_id' => $lang_id])@endif
@endif