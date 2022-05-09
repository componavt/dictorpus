            <tr>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="н/п">{{ $cycle->sequence_number }}</td>
                @endif
                @if (!$url_args['search_genre'])
                <td data-th="{{ trans('corpus.genre') }}">{{$cycle->genre->numberInList()}}. {{$cycle->genre->name}}</td>
                @endif
                <td data-th="{{ trans('messages.in_russian') }}">{{$cycle->name_ru}}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$cycle->name_en}}</td>
                <td data-th="{{ trans('navigation.texts') }}">
                @if($cycle->texts()->count())
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text/?search_cycle[]='.$cycle->id) }}">{{ $cycle->texts()->count() }}</a>
                @else
                    0
                @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/cycle/'.$cycle->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'cycle.destroy', 
                             'args'=>['id' => $cycle->id]])
                </td>
                @endif
            </tr>
