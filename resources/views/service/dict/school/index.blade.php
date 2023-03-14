<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.schooldict') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/essential_audio.css')!!}
    {!!Html::style('css/essential_audio_circle.css')!!}
    {!!Html::style('css/essential_audio_circle_mini.css')!!}
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        @include('service.dict.school._search_form',['url' => '/service/dict/school']) 
        @include('widgets.found_records', ['numAll'=>$numAll])

        @if ($lemmas)
        <table id="lemmasTable" class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th></th>
                <th>{{ trans('dict.lemma') }}</th>
                <th>{{ trans('dict.pos') }}</th>
                <th>{{ trans('dict.meanings') }}</th>
                <th>{{ trans('dict.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lemmas as $lemma)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td>
                @foreach ($lemma->audios as $audio_url)
                        @include('widgets.audio_decor', ['route'=>$audio_url])
                @endforeach
                </td>
                <td data-th="{{ trans('dict.lemma') }}">
                    <a href="{{ LaravelLocalization::localizeURL("/dict/lemma/".$lemma->id) }}">
                        {{$lemma->lemma}}
                    </a>
                </td>
                <td data-th="{{ trans('dict.pos') }}">
                        {{$lemma->pos->dict_code}}
                </td>
                <td data-th="{{ trans('dict.meanings') }}">
                @foreach ($lemma->meaningsWithLabel($label_id) as $meaning) 
                    <div id='meaning-{{$meaning->id}}'>
                        <i class="fa fa-times fa-lg clickable" data-delete="{{csrf_token()}}" onClick="removeLabelMeaning(this, {{$meaning->id}}, {{$label_id}}, '{{$meaning->getMultilangMeaningTextsString('ru')}}')"></i>
                        {{$meaning->getMultilangMeaningTextsString('ru')}}
                    @foreach ($meaning->examples as $example) 
                        @include('dict.example.view', ['meaning_id'=>$meaning->id, 'example_obj'=>$example])
                    @endforeach
                        <i id="add-example-for-{{$meaning->id}}" class="fa fa-plus fa-lg clickable link-color" onClick="addSimpleExample({{$meaning->id}})"></i>                        
                    </div>
                @endforeach
                </td>
                <td data-th="{{ trans('dict.status') }}">
                    <a class="set-status status{{$lemma->labelStatus($label_id)}}" id="status-{{$lemma->id}}" 
                       onClick="setStatus({{$lemma->id}}, {{$label_id}})"
                       data-old="{{$lemma->labelStatus($label_id)}}" 
                       data-new="{{$lemma->labelStatus($label_id) ? 0 : 1}}"></a>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
            {!! $lemmas->appends($url_args)->render() !!}
        @endif
    </div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/essential_audio.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/lemma.js')!!}
    {!!Html::script('js/new_dict.js')!!}
@stop

@section('jqueryFunc')
    selectWithLang('.select-dialect', "/dict/dialect/list", 'search_lang', '', true);
@stop

