@if($last_updated_texts)
                        <h2>{{trans('corpus.last_updated_texts')}}</h2>
                        <ol>
                        @foreach ($last_updated_texts as $text)
                        <li><a href="corpus/text/{{$text->id}}">{{$text->title}}</a> 
                            <i>({{$text->user}}, {{$text->updated_at}})</i></li> 
                        @endforeach
                        </ol>
@endif
