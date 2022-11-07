@extends('layouts.page')

@section('page_title')
{{ trans('collection.name_list')[3] }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
    <h2>{{trans('collection.motive_index')}}</h2>
    @foreach ($motypes as $motype)
    <p><b>{{$motype->code}}. {{$motype->name}}</b></p>
        <div style="margin-left: 20px;">
        @foreach($motype->motives()->whereNull('parent_id')->orderBy('code')->get() as $motive)
            <p>{{(int)$motive->code}}. {{$motive->name}}
            @if ($motive->texts()->count())
            (<a href="{{ LaravelLocalization::localizeURL('/corpus/collection/3/motives/'.$motive->id) }}">{{$motive->texts()->count()}}</a>)
            @endif
            </p>
            <div style="margin-left: 20px;">
            @foreach($motive->children()->orderBy('code')->get() as $element)
                <p>{{$element->code}}) {{$element->name}}
                @if ($element->texts()->count())
                (<a href="{{ LaravelLocalization::localizeURL('/corpus/collection/3/motives/'.$element->id) }}">{{$element->texts()->count()}}</a>)
                @endif
                </p>
            @endforeach
            </div>
        @endforeach
        </div>
    @endforeach
    </ol>
    
@stop
