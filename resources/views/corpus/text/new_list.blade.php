@if ($new_texts)
                        <h2>{{trans('corpus.new_texts')}}</h2>
                        <ol>
                        @foreach ($new_texts as $text)
                        <li><a href="corpus/text/{{$text->id}}">{{$text->title}}</a> 
                            <i>({{$text->user}}, {{$text->created_at}})</i></li> 
                        @endforeach
                        </ol>
@endif
