<?php \Carbon\Carbon::setLocale(LC_TIME, 'ru_RU.UTF-8');?>
@if ($new_lemmas)
                        <h2>{{trans('dict.new_lemmas')}}</h2>
                        <ol>
                        @foreach ($new_lemmas as $lemma)
                        <li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma')}}/{{$lemma->id}}">{{$lemma->lemma}}</a> 
                            ({{$lemma->user}}, <span class="date">{{$lemma->created_at->formatLocalized("%d %B %Y in %H:%m")}})</span></li> 
                        @endforeach
                        </ol>
                        @if ($limit)
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/new_list/')}}">{{trans('main.see_full_list')}}</a></p>
                        @endif
@endif