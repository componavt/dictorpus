@extends('layouts.page')

@section('page_title')
{{ trans('navigation.gramsets') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')       
        <p style="text-align: right">
        @if (User::checkAccess('ref.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/gramset/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('ref.edit'))
            </a>
        @endif
        </p>
        
        @include('widgets.founded_records', ['numAll'=>$numAll])
        
        @if ($gramsets && $numAll)
            {!! $gramsets->appends($url_args)->render() !!}
        <table class="table-bordered table-wide rwd-table wide-lg">
        <thead>
            <tr>
                <th>{{ trans('messages.sequence_number') }}</th>              
                @foreach ($gram_fields as $field)
                <th>{{ trans('dict.'.$field) }}</th>
                @endforeach
                                
                <th>{{ trans('dict.lemmas') }}</th>
                <th>{{ trans('dict.wordforms') }}</th>
                @if (User::checkAccess('ref.edit'))
                <th>{{ trans('dict.gramset_category') }}</th>
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($gramsets as $key=>$gramset)
            <tr>
                <td data-th="{{ trans('messages.sequence_number') }}">{{$gramset->sequence_number}}</td>
                @foreach ($gram_fields as $field)
                <td data-th="{{ trans('dict.'.$field) }}">
                    @if($gramset->{'gram_id_'.$field} && $gramset->{'gram'.ucfirst($field)})
                        {{$gramset->{'gram'.ucfirst($field)}->name_short}}
                    @endif
                </td>
                @endforeach

                <td data-th="{{ trans('dict.lemmas') }}">
                  <?php $count=sizeof($gramset->lemmas($url_args['search_pos'],$url_args['search_lang'])->groupBy('lemma_id')->get()); ?>
                  @if ($count)
                    <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}{{$args_by_get_for_out}}&search_gramset={{$gramset->id}}">
                        {{ $count }}
                    </a>
                  @else
                    {{ $count }}
                  @endif
                </td>

                <td data-th="{{ trans('dict.wordforms') }}">
                    <a href="{{ LaravelLocalization::localizeURL('/dict/wordform/') }}{{$args_by_get_for_out}}&search_gramset={{$gramset->id}}">
                        {{ $gramset->wordforms($url_args['search_pos'],$url_args['search_lang'])->count() }}
                    </a>
                </td>
                
                @if (User::checkAccess('ref.edit'))
                <td data-th="{{ trans('dict.gramset_category') }}">
                  {{ $gramset->gramsetCategory ? $gramset->gramsetCategory->name : ''}}
                </td>
                
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', ['route' => '/dict/gramset/'.$gramset->id.'/edit',
                                                           'is_button' => true,
                                                           'url_args' => $url_args,
                                                           'without_text' => true])
                    @include('widgets.form.button._delete', 
                             ['is_button' => true,
                              'route' => 'gramset.destroy', 
                              'args'=>['id' => $gramset->id],
                              'without_text' => true]) 
                </td>                
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
            {!! $gramsets->appends($url_args)->render() !!}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop
