@extends('layouts.page')

@section('page_title')
{{ trans('navigation.corpus_freq') }}
@endsection

@section('body')
    <p><a href="{{ LaravelLocalization::localizeURL('/corpus/word/freq_dict') }}">{{ trans('navigation.word_frequency') }}</a></p>
    <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text/freq_symb') }}">{{ trans('navigation.text_frequency') }}</a></p>
@endsection
