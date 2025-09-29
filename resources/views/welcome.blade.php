
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
                                {{trans('blob.in_numbers_text')}}
                                <div class="in_numbers-b">
                                    <span class="in_numbers-n"><a href="{{ LaravelLocalization::localizeURL('/stats/by_dict')}}">{{format_number($total_lemmas)}}</a></span>
                                    <span>{{trans_choice('blob.choice_articles',$total_lemmas, [], $locale)}}<br>{{trans('blob.about_words')}}</span>
                                </div>
                                <div class="in_numbers-b">
                                   <span class="in_numbers-n"><a href="{{ LaravelLocalization::localizeURL('/stats/by_dict')}}">{{format_number($total_texts)}}</a></span>
                                   <span>{{trans_choice('blob.choice_texts',$total_texts, [], $locale)}} 
                                       {!!trans('blob.on_dialects', ['count'=>'<a href="'.LaravelLocalization::localizeURL('/dict/dialect').'">'.$total_dialects.'</a>'])!!}</span>
                                </div>
                                <div class="in_numbers-b">
                                   <span class="in_numbers-n"><a href="/ru/stats">{{format_number($total_words)}}</a></span>
                                   <span>&nbsp;&nbsp;&nbsp;{{trans_choice('blob.choice_words',$total_words, [], $locale)}}</span>
                                </div>
                            </div>
                            
                @include('_form_simple_search', ['route'=>'simple_search'])
                    <div class="simple-search-by">
                        <div style='padding-right: 20px;'>
                            <input name="search_by_dict" type="checkbox" value="1" checked>
                            <label for="search_by_dict">{{trans('dict.in_dictionary')}}</label>
                        </div>
                        <div>
                            <input name="search_by_corpus" type="checkbox" value="1" checked>
                            <label for="search_by_corpus">{{trans('corpus.in_corpus')}}</label>
                        </div>
                    </div>   
                {!! Form::close() !!}
                            
                            <div id="last-added-lemmas" class="block-list">
                <img class="img-loading" src="{{ asset('images/loading.gif') }}">
                            </div>

                            <div id="last-added-texts" class="block-list">
                <img class="img-loading" src="{{ asset('images/loading.gif') }}">
                            </div>
                            
{{--                            <div class='mobile-b'>
                                <a href="https://play.google.com/store/apps/developer?id=Andrew+Krizhanovsky" style="padding-top: 7px;">
                                    <img src="/images/google_play.png"></a>
                                <div>{!!trans('blob.mobile-b')!!}</div>
                            </div> --}}
                            
                            @if ($locale == 'ru') 
                            <div class="block-list" style="margin-top: 20px;">
                            <p class="full-list">
                                <a href="https://vk.com/muamankieli
                                   ">{{ trans('navigation.marathon') }} "Lapsennu opastettu – ku kiveh kirjutettu"</a>
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
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/help.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    
    newListLoad('/dict/lemma/limited_new_list/', 'last-added-lemmas',{{$limit}});
    newListLoad('/corpus/text/limited_new_list/', 'last-added-texts',{{$limit}});
@stop
