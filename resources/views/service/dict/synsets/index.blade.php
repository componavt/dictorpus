@extends('layouts.page')

@section('page_title')
{{ trans('navigation.synsets') }}
@stop

@section('headExtra')
    <link media="all" type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.11.4/css/dataTables.bootstrap.min.css">
    {!!Html::style('css/table.css')!!}
    {!!Html::style('css/lemma.css')!!}
@stop

@section('body')  
    <p>
    @if (!empty($new_set_founded)) 
        <a href="{{ route('dict.synsets.find_new', $url_args) }}">Проверить найденные синсеты</a> |
    @endif
        <a href="{{ route('synset.create', $url_args) }}">Создать новый</a>
    </p>
    
    @include('service.dict.synsets._search_form',['url' => '/service/dict/synsets']) 
    @include('widgets.found_records', ['numAll'=>$numAll])

    @if ($synsets)
    <table id="synsetsTable" class="table table-striped rwd-table wide-md">
    <thead>
        <tr>
            <th>ID</th>
        @if (empty($url_args['search_pos']))
            <th>{{ trans('dict.pos') }}</th>
        @endif
            <th>{{ trans('dict.core') }}</th>
            <th>{{ trans('dict.periphery') }}</th>
            <th>{{ trans('corpus.comment') }}</th>
            <th>{{ trans('dict.potential_members') }}</th>
            <th>{{ trans('messages.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($synsets as $synset)
        <tr id="row-{{ $synset->id }}">
            <td data-th="ID">{{ $synset->id }}</td>
        @if (empty($url_args['search_pos']))
            <td data-th="{{ trans('dict.pos') }}">{{ $synset->pos->name }}</td>
        @endif
            <td data-th="{{ trans('dict.core') }}">
        @foreach ($synset->core as $meaning)
                <a href="{{ route('lemma.show', $meaning->lemma_id) }}">{{ $meaning->lemma->lemma }}</a><sup title="{{ $meaning->getMeaningTextLocale() }}">{{ $meaning->meaning_n}}</sup>        
        @endforeach
            </td>
            <td data-th="{{ trans('dict.periphery') }}">
        @foreach ($synset->periphery as $meaning)
                <a href="{{ route('lemma.show', $meaning->lemma_id) }}">{{ $meaning->lemma->lemma }}</a><sup title="{{ $meaning->getMeaningTextLocale() }}">{{ $meaning->meaning_n}}</sup>        
        @endforeach
            </td>
            <td data-th="{{ trans('corpus.comment') }}">{{ $synset->comment }}</td>
            <td data-th="{{ trans('navigation.potential_members') }}">
        @foreach ($synset->searchPotentialMembers() as $meaning)
                <a href="{{ route('lemma.show', $meaning->lemma_id) }}">{{ $meaning->lemma->lemma }}</a><sup title="{{ $meaning->getMeaningTextLocale() }}">{{ $meaning->meaning_n}}</sup>        
        @endforeach
            </td>
            <td data-th="{{ trans('messages.actions') }}" style="text-align:center; width: 100px;">
                <a class="set-status status{{ $synset->status }}" id="status-{{ $synset->id }}" 
                   onClick="setStatus({{ $synset->id }})"
                   title = "{{ $synset->status ? 'вернуть в черновики' : 'опубликовать' }}"
                   data-old="{{ $synset->status }}" 
                   data-new="{{ $synset->status ? 0 : 1 }}"></a>
                @include('widgets.form.button._edit', [
                    'is_button'=>true, 
                    'without_text' => 1,
                    'route' => '/dict/synset/'.$synset->id.'/edit'])
                @include('widgets.form.button._delete', [
                    'is_button'=>true, 
                    'without_text' => 1,
                    'route' => 'synset.destroy', 
                    'args'=>['id' => $synset->id]])
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
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop

