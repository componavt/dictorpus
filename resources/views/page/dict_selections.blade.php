@extends('layouts.page')

@section('page_title')
{{ trans('navigation.dict_selections') }}
@endsection

@section('body')
    <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/omonyms') }}">{{ trans('navigation.omonyms') }}</a></p>
    <p><a href="{{ LaravelLocalization::localizeURL('/dict/reverse_lemma') }}">{{ trans('navigation.reverse_dictionary') }}</a></p>
    <p><a href="{{ LaravelLocalization::localizeURL('/dict/reverse_lemma/inflexion_groups') }}">{{ trans('navigation.inflexion_groups') }}</a></p>
    <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma_wordform/affix_freq') }}">{{ trans('navigation.affix_freq') }}</a></p>
    <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma_wordform/pos_common_wordforms') }}">{{ trans('navigation.pos_common_wordforms') }}</a></p>
    <p><a href="{{ LaravelLocalization::localizeURL('/dict/concept/sosd') }}">{{ trans('navigation.sosd') }}</a></p>
@endsection
