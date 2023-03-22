
@extends('layouts.master')

@section('title')
{{ trans('main.site_title') }}
@endsection

@section('headExtra')
    {!!Html::style('css/fancybox.css')!!}
@stop

@section('content')
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-7">
                            <h1>{{ trans('navigation.about_project_vepkar') }}</h1>
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
                            <a data-fancybox="gallery" href="/images/participants/big/2021-09.jpg" data-caption="{{trans('navigation.participants')}}">
                                <img class="img-fluid img-responsive" src="/images/participants/2021-09.jpg" alt="">
                            </a>
                            <div style="text-align: center">
                                <a href="page/participants">{{trans('navigation.participants')}}</a>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <h2>{{trans('blob.corpus_means_title')}}</h2>
                            <div class="corpus_means_text">{{trans('blob.corpus_means_text')}}</div>
                            
                            <div class="in_numbers">
                                <h2>{{trans('blob.in_numbers_title')}}</h2>
                                {!!trans('blob.in_numbers_text',[
                                        'total_dialects'=>$total_dialects,
                                        'total_words'=>number_format($total_words, 0, ',', ' '),
                                        'words' => $words_choice,
                                        'total_lemmas'=>number_format($total_lemmas, 0, ',', ' '),
                                        'lemmas' => $lemmas_choice,
                                        'texts' => $texts_choice,
                                        'total_texts'=>number_format($total_texts, 0, ',', ' ')])!!}
                            </div>
                            <div id="last-added-lemmas" class="block-list">
                <img class="img-loading" src="{{ asset('images/loading.gif') }}">
                            </div>

                            <div id="last-added-texts" class="block-list">
                <img class="img-loading" src="{{ asset('images/loading.gif') }}">
                            </div>
                            
                            <?php $locale = LaravelLocalization::getCurrentLocale();?>
                            @if ($locale == 'ru') 
                            <div class='mobile-b'>
                                <a href="https://play.google.com/store/apps/details?id=vepkar.test"><img src="/images/google_play.png"></a>
                                <div>{!!trans('blob.mobile-b')!!}</div>
                            </div>
                            
                            <div class="block-list" style="margin-top: 20px;">
                            <p class="full-list">
                                <a href="https://vk.com/speechvepkar
                                   ">Марафон записей вепсской и карельской речи</a>
                            </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
@endsection

@section('footScriptExtra')
    {!!Html::script('js/new_list_load.js')!!}
    {!!Html::script('js/fancybox.umd.js')!!}
@stop

@section('jqueryFunc')
    newListLoad('/dict/lemma/limited_new_list/', 'last-added-lemmas',{{$limit}});
    newListLoad('/corpus/text/limited_new_list/', 'last-added-texts',{{$limit}});
@stop
