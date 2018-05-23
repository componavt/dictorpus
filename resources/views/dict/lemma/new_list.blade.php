@if ($new_lemmas)
                        <h2>{{trans('dict.new_lemmas')}}</h2>
                        @if ($limit)
                            <ol>
                            @foreach ($new_lemmas as $lemma)
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma')}}/{{$lemma->id}}">{{$lemma->lemma}}</a> 
                                ({{$lemma->user}}, <span class="date">{{$lemma->created_at->formatLocalized(trans('main.datetime_format'))}})</span></li> 
                            @endforeach
                            </ol>
                            <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/new_list/')}}">{{trans('main.see_full_list')}}</a></p>
                        @else
                            @foreach ($new_lemmas as $time =>$lemmas)
                            <p class="date">{{$time}}</p>
                            <ol>
                                @foreach ($lemmas as $lemma)
                                <li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma')}}/{{$lemma->id}}">{{$lemma->lemma}}</a> 
                                    ({{$lemma->user}}, <span class="date">{{$lemma->created_at->formatLocalized("%H:%M")}})</li> 
                                @endforeach
                            </ol>
                            @endforeach
                        @endif
@endif