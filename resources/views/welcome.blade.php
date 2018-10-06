@extends('layouts.master')

@section('title')
{{ trans('main.site_title') }}

@endsection

@section('content')
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-7">
                            <h1>{{ trans('navigation.about_project') }} VepKar</h1>
                            @if ($video)
                                @include('widgets.youtube',
                                        ['width' => '100%',
                                         'height' => '270',
                                         'video' => $video->youtube_id
                                        ])
                                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$video->text_id) }}">{{$video->text->title}}</a></p>
                            @endif
                            <div class="text-page">        
                            {!! trans('blob.welcome_text') !!}
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <h2>{{trans('blob.corpus_means_title')}}</h2>
                            <div class="corpus_means_text">{{trans('blob.corpus_means_text')}}</div>
                            
                            <div class="in_numbers">
                                <h2>{{trans('blob.in_numbers_title')}}</h2>
                                {!!trans('blob.in_numbers_text',[
                                        'total_dialects'=>$total_dialects,
                                        'total_lemmas'=>$total_lemmas,
                                        'lemmas' => $lemmas_choice,
                                        'texts' => $texts_choice,
                                        'total_texts'=>$total_texts])!!}
                            </div>
                            <div id="last-updated-lemmas" class="block-list">
                            </div>

                            <div id="last-updated-texts" class="block-list">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@section('footScriptExtra')
    {!!Html::script('js/new_list_load.js')!!}
@stop

@section('jqueryFunc')
    newListLoad('/dict/lemma/limited_updated_list/', 'last-updated-lemmas',{{$limit}});
    newListLoad('/corpus/text/limited_updated_list/', 'last-updated-texts',{{$limit}});
@stop
