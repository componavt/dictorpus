@if ($new_texts)
                        <h2>{{trans('corpus.new_texts')}}</h2>
                        <ol>
                        @foreach ($new_texts as $text)
                        <li><a href="{{ LaravelLocalization::localizeURL('corpus/text')}}/{{$text->id}}">{{$text->title}}</a> 
                            <i>({{$text->user}}, {{$text->created_at}})</i></li> 
                        @endforeach
                        </ol>
                        @if ($limit)
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text/new_list/')}}">{{trans('main.see_full_list')}}</a></p>
                        @endif
@endif
