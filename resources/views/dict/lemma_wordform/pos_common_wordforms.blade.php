@extends('layouts.page')

@section('page_title')
{{ trans('navigation.pos_common_wordforms') }}
@stop

@section('body')     
<p><b>{{trans('dict.lang')}}:</b> {{$search_lang}}</p>
<p><b>{{trans('dict.wordforms_total')}}:</b> {{$wordforms_total}}</p>
<p><b>{{trans('dict.wordforms_grouped_total')}}:</b> {{$wordforms_grouped_total}}</p>
<p><b>{{trans('dict.unique_wordforms_total')}}:</b> {{$unique_wordforms_total}}</p>
<p><b>{{trans('dict.common_wordforms_total')}}:</b> {{$common_wordforms_total}}, {{trans('messages.including')}}
    @foreach($common_wordforms_total_counts as $pos_count=>$w_count)
    <br><b>{{$w_count}}</b> {{trans('dict.in_pos', ['count'=>$pos_count])}}
    @endforeach
</p>
@stop

