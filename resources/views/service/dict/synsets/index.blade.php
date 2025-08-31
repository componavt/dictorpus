@extends('layouts.page')

@section('page_title')
{{ trans('navigation.synsets') }}
@stop

@section('headExtra')
    <link media="all" type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.11.4/css/dataTables.bootstrap.min.css">
    {!!Html::style('css/table.css')!!}
@stop

@section('body')  
    <p>
    @if (!empty($new_set_founded)) 
        <a href="{{ route('dict.synsets.find_new') }}">Проверить найденный синсет</a> |
    @endif
        <a href="#">Создать новый</a>
    </p>
    
    @include('service.dict.synsets._search_form',['url' => '/service/dict/synsets']) 
    @include('widgets.found_records', ['numAll'=>$numAll])

    @if ($synsets)
    <table id="synsetsTable" class="table table-striped rwd-table wide-md">
    <thead>
        <tr>
            <th>ID</th>
            <th>{{ trans('dict.pos') }}</th>
            <th>{{ trans('dict.core') }}</th>
            <th>{{ trans('dict.periphery') }}</th>
            <th>{{ trans('dict.potential_members') }}</th>
            <th>{{ trans('messages.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($synsets as $synset)
        <tr id="row-{{ $synset->id }}">
            <td data-th="ID">{{ $synset->id }}</td>
            <td data-th="{{ trans('dict.pos') }}">
            </td>
            <td data-th="{{ trans('dict.core') }}">
            </td>
            <td data-th="{{ trans('dict.periphery') }}">
            </td>
            <td data-th="{{ trans('navigation.potential_members') }}">
            </td>
            <td data-th="{{ trans('messages.actions') }}" style="text-align:center">
                <a class="set-status status{{ $synset->status }}" id="status-{{ $synset->id }}" 
                   onClick="setStatus({{ $synset->id }})"
                   title = "{{ $synset->status ? 'вернуть в черновики' : 'опубликовать' }}"
                   data-old="{{ $synset->status }}" 
                   data-new="{{ $synset->status ? 0 : 1 }}"></a>
            </td>
        </tr>
        @endforeach
    </tbody>
    </table>
        {!! $synsets->appends($url_args)->render() !!}
    @endif
@stop

@section('footScriptExtra')
    <script src="//cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.11.4/js/dataTables.bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/sorting/numeric-comma.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/type-detection/numeric-comma.js"></script>
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/synset.js')!!}
@stop

@section('jqueryFunc')
@stop

