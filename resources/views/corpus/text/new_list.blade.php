@if ($new_texts)
                        <h2>{{trans('corpus.new_texts')}}</h2>
                        <ol>
                        @foreach ($new_texts as $text)
                        <li><a href="{{ LaravelLocalization::localizeURL('corpus/text')}}/{{$text->id}}">{{$text->title}}</a> 
                            ({{$text->user}}, <span class="date">{{$text->created_at->formatLocalized(trans('main.datetime_format'))}})</span></li> 
                        @endforeach
                        </ol>
                        @if ($limit)
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text/new_list/')}}">{{trans('main.see_full_list')}}</a></p>
                        @endif
@endif
