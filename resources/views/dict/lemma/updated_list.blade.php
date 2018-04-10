@if($last_updated_lemmas)
                        <h2>{{trans('dict.last_updated_lemmas')}}</h2>
                        <ol>
                        @foreach ($last_updated_lemmas as $lemma)
                        <li><a href="dict/lemma/{{$lemma->id}}">{{$lemma->lemma}}</a> 
                            <i>({{$lemma->user}}, {{$lemma->updated_at}})</i></li> 
                        @endforeach
                        </ol>
                        @if ($limit)
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/updated_list/')}}">{{trans('main.see_full_list')}}</a></p>
                        @endif
@endif
