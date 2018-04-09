@extends('layouts.master')

@section('title')
{{ trans('main.site_title') }}
@endsection

@section('content')
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('navigation.about_project') }}</div>

                <div class="panel-body">
                    {!! trans('blob.welcome_text') !!}
                    
                    <div class="last-created-lemma">
                        <h4>{{trans('dict.new_lemmas')}}</h4>
                        <ol>
                        @foreach ($new_lemmas as $lemma)
                        <li><a href="dict/lemma/{{$lemma->id}}">{{$lemma->lemma}}</a> 
                            <i>({{$lemma->user}}, {{$lemma->created_at}})</i></li> 
                        @endforeach
                        </ol>
                    </div>
                    
                    @if($last_updated_lemmas)
                    <div class="last-updated-lemma">
                        <h4>{{trans('dict.last_updated_lemmas')}}</h4>
                        <ol>
                        @foreach ($last_updated_lemmas as $lemma)
                        <li><a href="dict/lemma/{{$lemma->id}}">{{$lemma->lemma}}</a> 
                            <i>({{$lemma->user}}, {{$lemma->updated_at}})</i></li> 
                        @endforeach
                        </ol>
                    </div>
                    @endif
                    
                    <div class="last-created-text">
                        <h4>{{trans('corpus.new_texts')}}</h4>
                        <ol>
                        @foreach ($new_texts as $text)
                        <li><a href="corpus/text/{{$text->id}}">{{$text->title}}</a> 
                            <i>({{$text->user}}, {{$text->created_at}})</i></li> 
                        @endforeach
                        </ol>
                    </div>
                    
                    @if($last_updated_texts)
                    <div class="last-updated-text">
                        <h4>{{trans('corpus.last_updated_texts')}}</h4>
                        <ol>
                        @foreach ($last_updated_texts as $text)
                        <li><a href="corpus/text/{{$text->id}}">{{$text->title}}</a> 
                            <i>({{$text->user}}, {{$text->updated_at}})</i></li> 
                        @endforeach
                        </ol>
                    </div>
                    @endif
                </div>
            </div>
@endsection
