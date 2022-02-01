            <tr>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="н/п">{{ $plot->sequence_number }}</td>
                @endif
                @if (!$url_args['search_genre'])
                <td data-th="{{ trans('corpus.genre') }}">{{$plot->genre->numberInList()}}. {{$plot->genre->name}}</td>
                @endif
                <td data-th="{{ trans('messages.in_russian') }}">{{$plot->name_ru}}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$plot->name_en}}</td>
                <td data-th="{{ trans('navigation.texts') }}">
                @if($plot->texts)
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text/?search_plot[]='.$plot->id) }}">{{ $plot->texts()->count() }}</a>
                @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/plot/'.$plot->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'plot.destroy', 
                             'args'=>['id' => $plot->id]])
                </td>
                @endif
            </tr>
