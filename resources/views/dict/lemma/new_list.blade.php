@if ($new_lemmas)
                        <h2>{{trans('dict.new_lemmas')}}</h2>
                        <ol>
                        @foreach ($new_lemmas as $lemma)
                        <li><a href="dict/lemma/{{$lemma->id}}">{{$lemma->lemma}}</a> 
                            <i>({{$lemma->user}}, {{$lemma->created_at}})</i></li> 
                        @endforeach
                        </ol>
@endif