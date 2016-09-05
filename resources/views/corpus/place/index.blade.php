@extends('layouts.master')

@section('title')
{{ trans('corpus.place_list') }}
@stop

@section('content')
        <h2>{{ trans('corpus.place_list') }}</h2>
            
        <table class="table">
        <thead>
            <tr>
                <th>{{ trans('corpus.region') }}</th>
                <th>{{ trans('corpus.district') }}</th>
                <th>{{ trans('corpus.title') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($places as $place)
            <tr>
                <td>{{$place->region->name}}</td>
                <td>
                    @if ($place->district_id)
                        {{$place->district->name}}
                    @endif
                </td>
                <td>
                    @if ($place->name_en)
                    <b>{{ \App\Models\Dict\Lang::getNameByCode('en') }}:</b> {{ $place->name_en }}<br>
                    @endif
                    @if ($place->name_ru)
                    <b>{{ \App\Models\Dict\Lang::getNameByCode('ru') }}:</b> {{ $place->name_ru }}<br>
                    @endif
                    
                    @foreach($place->other_names as $other_name)
                    <b>{{ \App\Models\Dict\Lang::find($other_name->lang_id)->name }}:</b> {{ $other_name->name }}<br>
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>
@stop


