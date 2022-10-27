            <tr>
                @if (!$url_args['search_genre'])
                <td data-th="{{ trans('corpus.genre') }}">{{$motive->genreName(true)}}</td>
                @endif
                <td data-th="н/п">{{$motive->full_code}}</td>
                <td data-th="{{ trans('messages.in_russian') }}">{{$motive->name_ru}}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$motive->name_en}}</td>
                <td data-th="{{ trans('navigation.texts') }}">
                @if($motive->texts)
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text/?search_motive[]='.$motive->id) }}">{{ $motive->texts()->count() }}</a>
                @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/motive/'.$motive->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'motive.destroy', 
                             'args'=>['id' => $motive->id]])
                </td>
                @endif
            </tr>
