@extends('layouts.page')

@section('page_title')
Отобранные тексты для карты "Праздничная культура Южной Карелии"
@endsection

@section('body')        
        @foreach ($regions as $region_name=>$districts) 
        <h2>{{ $region_name }}</h2>
            @foreach ($districts as $district_name=>$places) 
        <h3>{{ $district_name }}</h3>
                @foreach ($places as $place_id=>$place_name) 
        <h4 style="margin-bottom: 0">{{ $place_name }}</h4>
                    @foreach ($text_places[$place_id] as $text) 
        <p>{{$text->id}}. <a href="{{ route('text.show', $text->id) }}">{{ $text->title }}</a></p>
                    @endforeach                
                @endforeach
            @endforeach
        @endforeach
@endsection
