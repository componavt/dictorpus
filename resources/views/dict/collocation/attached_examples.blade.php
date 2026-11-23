@extends('layouts.page')

@section('page_title')
{{ trans('collocation_examples.attached_title') }}
@stop

@section('headExtra')
{!! css('text') !!}
{!! css('meaning-examples') !!}
{!! css('meaning-example-form') !!}
@stop

@section('body')
@if ($synset_id)
    @include('dict.synset._nav', ['active' => 'collocations'])
@else
    <p><a href="{{ LaravelLocalization::localizeURL('/dict/meaning/'.$source->id.'/collocations') }}">{{ trans('collocations.title') }}</a></p>
@endif

<h3>
    {{ $source->lemma->lemma }}<sup>{{ $source->meaning_n }}</sup> +
    {{ $collocation->collocateMeaning->lemma->lemma }}<sup>{{ $collocation->collocateMeaning->meaning_n }}</sup>
</h3>

@if (!count($items))
<p>{{ trans('collocation_examples.attached_empty') }}</p>
@endif

@foreach ($items as $item)
<div class="example-card">
    <div class="example-card-text">
        <span class="meaning-example-sentence">{!! $item['html'] !!}</span>
    </div>
    <div class="example-card-footer">
        @foreach ($item['groups'] as $group_index => $group)
        <span title="{{ $group['title'] }}">{{ $group['label'] }}</span>{{ $group_index < count($item['groups']) - 1 ? ',' : '' }}
        @endforeach
        @if ($item['text'])
        (<a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$item['text']->id) }}">{{ $item['text']->title }}</a>)
        @endif
    </div>
</div>
@endforeach
@stop