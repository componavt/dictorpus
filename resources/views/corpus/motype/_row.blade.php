            <tr>
                @if (!$url_args['search_genre'])
                <td data-th="{{ trans('corpus.genre') }}">{{$motype->genre->name}}</td>
                @endif
                <td data-th="н/п">{{$motype->code}}</td>
                <!--td data-th="{{ trans('corpus.parent') }}">{{$motype->parent->name ?? ''}}</td-->
                <td data-th="{{ trans('messages.in_russian') }}">{{$motype->name_ru}}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$motype->name_en}}</td>
                <td data-th="{{ trans('navigation.texts') }}">
{{--                @if($motype->texts)
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text/?search_motype[]='.$motype->id) }}">{{ $motype->texts()->count() }}</a>
                @endif--}}
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/motype/'.$motype->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'motype.destroy', 
                             'args'=>['id' => $motype->id]])
                </td>
                @endif
            </tr>
