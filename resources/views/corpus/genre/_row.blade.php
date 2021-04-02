            <tr>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="н/п">{{ $genre->sequence_number }}</td>
                @endif
                @if (!$url_args['search_corpus'])
                <td data-th="{{ trans('corpus.corpus') }}">{{$corpus_name}}</td>
                @endif
                <!--td data-th="{{ trans('corpus.parent') }}">{{$genre->parent->name ?? ''}}</td-->
                <td data-th="{{ trans('messages.in_russian') }}"{!!$with_div ? ' style="padding-left: 20px;"' : ''!!}>{{$count}}. {{$genre->name_ru}}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$genre->name_en}}</td>
                <td data-th="{{ trans('navigation.texts') }}">
                @if($genre->texts)
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text/?search_genre[]='.$genre->id) }}">{{ $genre->texts()->count() }}</a>
                @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/genre/'.$genre->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'genre.destroy', 
                             'args'=>['id' => $genre->id]])
                </td>
                @endif
            </tr>
