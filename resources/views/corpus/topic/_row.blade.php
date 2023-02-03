            <tr>
                <td data-th="No">{{ $topic->number_in_genres }}</td>
                <td data-th="{{ trans('messages.in_russian') }}">{{$topic->name_ru}}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$topic->name_en}}</td>
                @if (!$url_args['search_plot'])
                <td data-th="{{ trans('corpus.plot') }}">{!!$topic->plotsToString(';<br>')!!}</td>
                @endif
                <td data-th="{{ trans('navigation.texts') }}">
                @if($topic->texts)
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text/?search_topic[]='.$topic->id) }}">{{ $topic->texts()->count() }}</a>
                @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/topic/'.$topic->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'topic.destroy', 
                             'args'=>['id' => $topic->id]])
                </td>
                @endif
            </tr>
