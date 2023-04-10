@extends('layouts.page')

@section('page_title')
{{ trans('search.search_simple_title') }}
@stop

@section('body')
        @include('_form_simple_search', ['route'=>'simple_search'])
            <div class="row">
                <div class='col-sm-6'>
                    <input name="search_by_dict" type="checkbox" value="1"{{$search_by_dict ? ' checked' : ''}}>
                    <label for="search_by_dict">{{trans('dict.in_dictionary')}}</label>
                    @include('dict.lemma.search._simple_found_records', 
                        ['count'=>'<a href="'.route('lemma.simple_search',['search_w'=>$search_w]).'"><b>'.format_number($lemma_total).'</b></a>'])
                </div>
                <div class='col-sm-6'>
                    <input name="search_by_corpus" type="checkbox" value="1"{{$search_by_corpus ? ' checked' : ''}}>
                    <label for="search_by_corpus">{{trans('corpus.in_corpus')}}</label>
                    @include('corpus.text.search._simple_found_records', 
                        ['count'=>'<a href="'.route('text.simple_search',['search_w'=>$search_w]).'"><b>'.format_number($text_total).'</b></a>'])
                </div>
            </div>   
        {!! Form::close() !!}

@stop

@section('footScriptExtra')
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/help.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
@stop
