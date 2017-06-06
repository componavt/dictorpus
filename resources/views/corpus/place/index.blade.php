<?php $list_count = $limit_num * ($page-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('corpus.place_list') }}
@stop

@section('content')
        <h2>{{ trans('corpus.place_list') }}</h2>
            
        <p>
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/place/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>
        
        {!! Form::open(['url' => '/corpus/place/', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form._formitem_text', 
                ['name' => 'search_id', 
                'value' => $search_id,
                'attributes'=>['size' => 3,
                               'placeholder' => 'ID']])
         @include('widgets.form._formitem_text', 
                ['name' => 'place_name', 
                 'special_symbol' => true,
                'value' => $place_name,
                'attributes'=>['size' => 15,
                               'placeholder' => trans('corpus.title')]])
        @include('widgets.form._formitem_select', 
                ['name' => 'region_id', 
                 'values' => $region_values,
                 'value' => $region_id,
                 'attributes' => ['placeholder' => trans('corpus.region')]]) 
                                  
        @include('widgets.form._formitem_select', 
                ['name' => 'district_id', 
                 'values' => $district_values,
                 'value' => $district_id,
                 'attributes' => ['placeholder' => trans('corpus.district')]]) 
                                  
        <br>         
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
        
        {{trans('messages.show_by')}}
        @include('widgets.form._formitem_text', 
                ['name' => 'limit_num', 
                'value' => $limit_num, 
                'attributes'=>['size' => 5,
                               'placeholder' => trans('messages.limit_num')]]) {{ trans('messages.records') }}
        {!! Form::close() !!}

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>
        
        <table class="table">
        <thead>
            <tr>
                <th>{{ trans('corpus.region') }}</th>
                <th>{{ trans('corpus.district') }}</th>
                <th>{{ trans('corpus.title') }}</th>
                <th>{{ trans('navigation.texts') }}</th>
                <th>{{ trans('navigation.informants') }}</th>
                @if (User::checkAccess('corpus.edit'))
                <th colspan="2"></th>
                @endif
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
                <td>
                    @if($place->texts)
                        {{ $place->texts()->count() }}
                    @endif
                </td>
                <td>
                    @if($place->informants)
                        {{ $place->informants()->count() }}
                    @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td>
                    @include('widgets.form._button_edit', ['is_button'=>true, 'route' => '/corpus/place/'.$place->id.'/edit'])
                 </td>
                <td>
                    @include('widgets.form._button_delete', ['is_button'=>true, $route = 'place.destroy', 'id' => $place->id])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $places->appends(['limit_num' => $limit_num,
                              'place_name' => $place_name,
                              'region_id'=>$region_id,
                              'district_id'=>$district_id])->render() !!}
    </div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    recDelete('{{ trans('messages.confirm_delete') }}', '/corpus/informant');
@stop