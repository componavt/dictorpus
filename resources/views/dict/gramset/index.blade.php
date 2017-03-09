@extends('layouts.master')

@section('title')
{{ trans('navigation.gramsets') }}
@stop

@section('content')
        <h2>{{ trans('navigation.gramsets') }}</h2>
        
        <p style="text-align: right">
        @if (User::checkAccess('ref.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/gramset/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('ref.edit'))
            </a>
        @endif
        </p>
        
        {!! Form::open(['url' => '/dict/gramset/', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form._formitem_select', 
                ['name' => 'search_pos', 
                 'values' =>$pos_values,
                 'value' => $url_args['search_pos'],
                 'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 
        @include('widgets.form._formitem_select', 
                ['name' => 'search_lang', 
                 'values' =>$lang_values,
                 'value' => $url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]]) 
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])

        {{trans('messages.show_by')}}
        @include('widgets.form._formitem_text',
                ['name' => 'limit_num',
                'value' => $url_args['limit_num'],
                'attributes'=>['size' => 5,
                               'placeholder' => trans('messages.limit_num') ]]) {{ trans('messages.records') }}
        {!! Form::close() !!}

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>

        @if ($gramsets && $numAll)
            {!! $gramsets->appends($url_args)->render() !!}
        <table class="table">
        <thead>
            <tr>
                <th>{{ trans('messages.sequence_number') }}</th>              
                @foreach ($gram_fields as $field)
                <th>{{ trans('dict.'.$field) }}</th>
                @endforeach
                
                @if (User::checkAccess('ref.edit'))
                <th colspan="2"></th>
                @endif
                
                <th>{{ trans('dict.lemmas') }}</th>
                <th>{{ trans('dict.wordforms') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gramsets as $key=>$gramset)
            <tr>
                <td>{{$gramset->sequence_number}}</td>
                @foreach ($gram_fields as $field)
                <td>
                    @if($gramset->{'gram_id_'.$field} && $gramset->{'gram'.ucfirst($field)})
                        {{$gramset->{'gram'.ucfirst($field)}->name_short}}
                    @endif
                </td>
                @endforeach

                @if (User::checkAccess('ref.edit'))
                <td>
                    @include('widgets.form._button_edit', ['route' => '/dict/gramset/'.$gramset->id.'/edit',
                                                           'is_button' => true,
                                                           'url_args' => $url_args,
                                                           'without_text' => true])
                </td>
                <td>
                    @include('widgets.form._button_delete', 
                             ['is_button' => true,
                              'route' => 'gramset.destroy', 
                              'id' => $gramset->id,
                              'without_text' => true]) 
                </td>
                
                <td>
                  <?php $count=sizeof($gramset->lemmas($url_args['search_pos'],$url_args['search_lang'])->groupBy('lemma_id')->get()); ?>
                  @if ($count)
                    <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}{{$args_by_get}}&search_gramset={{$gramset->id}}">
                        {{ $count }}
                    </a>
                  @else
                    {{ $count }}
                  @endif
                </td>

                <td>
                  {{ $gramset->wordforms($url_args['search_pos'],$url_args['search_lang'])->count() }}
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
    recDelete('{{ trans('messages.confirm_delete') }}', '/dict/gramset');
@stop
