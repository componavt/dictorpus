@extends('layouts.page')

@section('page_title', @trans('navigation.monuments'))

@section('body')
<p>
    <a href="{{ LaravelLocalization::localizeURL('/corpus/monument/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>

    @if (User::checkAccess('corpus.edit'))
    | @include('widgets.form.button._edit', ['route' => '/corpus/monument/'.$monument->id.'/edit'])
    | @include('widgets.form.button._delete', ['route' => 'monument.destroy', 'args'=>['id' => $monument->id]])
    @else
    | {{ trans('messages.edit') }} | {{ trans('messages.delete') }}
    @endif
    | <a href="">{{ trans('messages.history') }}</a>
</p>

@if ($monument->author)
<h3>{{ $monument->author }}</h3>
@endif
<h2>{{ $monument->title }}</h2>

@if ($monument->langs)
<p><b>{{ mb_ucfirst(trans('monument.lang')) }}:</b> {{ $monument->langsAndDialectsToString() }}</p>
@endif

@if ($monument->place)
<p><b>{{ mb_ucfirst(trans('monument.place')) }}:</b> {{ $monument->place }}</p>
@endif

@if ($monument->publ_date)
<p><b>{{ mb_ucfirst(trans('monument.publ_date')) }}:</b> {{ $monument->publ_date }}</p>
@endif

@if ($monument->pages)
<p><b>{{ mb_ucfirst(trans('monument.pages')) }}:</b> {{ $monument->pages }}</p>
@endif

@if ($monument->bibl_descr)
<p><b>{{ mb_ucfirst(trans('monument.bibl_descr')) }}:</b> {{ $monument->bibl_descr }}</p>
@endif

@if ($monument->graphic_id)
<p><b>{{ mb_ucfirst(trans('monument.graphic')) }}:</b> {{ $monument->graphic_id && !empty(trans('monument.graphic_values')[$monument->graphic_id]) 
            ? trans('monument.graphic_values')[$monument->graphic_id] : null }}</p>
@endif

@if ($monument->has_trans !== null)
<p><b>{{ mb_ucfirst(trans('monument.has_trans')) }}:</b> {{ !empty(trans('monument.has_trans_values')[$monument->has_trans]) 
            ? trans('monument.has_trans_values')[$monument->has_trans] : null }}</p>
@endif

@if ($monument->volume)
<p><b>{{ mb_ucfirst(trans('monument.volume')) }}:</b> {{ $monument->volume }}</p>
@endif

@if ($monument->types)
<p><b>{{ mb_ucfirst(trans('monument.type')) }}:</b> {{ $monument->typesToString() }}</p>
@endif

@if ($monument->is_printed !== null)
<p><b>{{ mb_ucfirst(trans('monument.is_printed')) }}:</b> {{ isset(trans('monument.is_printed_values')[$monument->is_printed]) 
            ? trans('monument.is_printed_values')[$monument->is_printed] : null }}</p>
@endif

@if ($monument->is_full !== null)
<p><b>{{ mb_ucfirst(trans('monument.is_full')) }}:</b> {{ isset(trans('monument.is_full_values')[$monument->is_full]) 
            ? trans('monument.is_full_values')[$monument->is_full] : null }}</p>
@endif

@if ($monument->dcopy_link)
<p><b>{{ mb_ucfirst(trans('monument.dcopy_link')) }}:</b> {!! text_to_html($monument->dcopy_link) !!}</p>
@endif

@if ($monument->publ)
<p><b>{{ mb_ucfirst(trans('monument.publ')) }}:</b> {!! text_to_html($monument->publ) !!}</p>
@endif

@if ($monument->study)
<p><b>{{ mb_ucfirst(trans('monument.study')) }}:</b> {!! text_to_html($monument->study) !!}</p>
@endif

@if ($monument->archive)
<p><b>{{ mb_ucfirst(trans('monument.archive')) }}:</b> {{ $monument->archive }}</p>
@endif

@if ($monument->comment)
<p><b>{{ mb_ucfirst(trans('monument.comment')) }}:</b> {{ $monument->comment }}</p>
@endif

@stop

@section('footScriptExtra')
{!!Html::script('js/rec-delete-link.js')!!}
{!!Html::script('js/meaning.js')!!}
@stop

@section('jqueryFunc')
recDelete('{{ trans('messages.confirm_delete') }}');
/* loadPhoto('monument', {{$monument->id}}, '/dict/monument/{{$monument->id}}/photo_preview');*/
@stop