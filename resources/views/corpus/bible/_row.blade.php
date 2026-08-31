            <tr>
                <td data-th="No">{{ $bible->sequence_number }}</td>
                <td data-th="{{ trans('messages.in_russian') }}">{{$bible->name_ru}}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$bible->name_en}}</td>
                <td data-th="{{ trans('navigation.texts') }}">
                @if($bible->texts()->count())
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text/?search_bible[]='.$bible->id) }}">{{ $bible->texts()->count() }}</a>
                @else
                    0
                @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/bible/'.$bible->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'bible.destroy', 
                             'args'=>['id' => $bible->id]])
                </td>
                @endif
            </tr>
