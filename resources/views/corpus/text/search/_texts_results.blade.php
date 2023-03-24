<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
        @if ($numAll)
        <table class="table-bordered table-striped table-wide rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
            @if (!$url_args['search_lang'])
                <th>{{ trans('dict.lang') }}</th>
            @endif
            @if (!$url_args['search_dialect'])
                <th>{{ trans('dict.dialect') }}</th>
            @endif
            @if (!$url_args['search_corpus'])
                <th>{{ trans('corpus.corpus') }}</th>
            @endif
            @if (!$url_args['search_genre'])
                <th>{{ trans('corpus.genre') }}</th>
            @endif
                <th>{{ trans('corpus.title') }}</th>
                @if (!$url_args['search_word'])
                <th>{{ trans('messages.translation') }}</th>
                @else
                <th style='text-align: center'>{{ trans('corpus.sentences') }}</th>
                @endif
                @if (User::checkAccess('corpus.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($texts as $text)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
            @if (!$url_args['search_lang'])
                <td data-th="{{ trans('dict.lang') }}">{{$text->lang->name}}</td>
            @endif
            @if (!$url_args['search_dialect'])
                <td data-th="{{ trans('dict.dialect') }}">
                    @if($text->dialects)
                        @foreach ($text->dialects as $dialect)
                        {{$dialect->name}}<br>
                        @endforeach
                        
                    @endif
                </td>
            @endif
            @if (!$url_args['search_corpus'])
                <td data-th="{{ trans('corpus.corpus') }}">{{$text->corpus->name}}</td>
            @endif
            @if (!$url_args['search_genre'])
                <td data-th="{{ trans('corpus.genre') }}">{{$text->genresToString()}}</td>
            @endif
                <td data-th="{{ trans('corpus.title') }}">
                    {{ $text->authorsToString() ? $text->authorsToString().'.' : '' }}
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}{{$args_by_get}}">{!!highlight($text->title, $url_args['search_w'], 'search-word')!!}</a>
                @if ($url_args['search_word'] && $text->transtext)
                    <br>({!!highlight($text->transtext->title, $url_args['search_w'], 'search-word')!!})
                @endif
                </td>
                @if (!$url_args['search_word'])
                <td data-th="{{ trans('messages.translation') }}">
                    @if ($text->transtext)
                    {{ $text->transtext->authorsToString() ? $text->transtext->authorsToString().'.' : '' }}
                    {!!highlight($text->transtext->title, $url_args['search_w'], 'search-word')!!}
                    @endif
                </td>
                @else
                <td data-th="{{ trans('corpus.sentences') }}">                    
                    @foreach ($text->sentencesFromText($url_args['search_word']) as $s_id => $sentence)
                    <ol start="{{$s_id}}">
                        <li>@include('corpus.text.show_sentence',['count'=>$s_id])</li>
                    </ol>
                    @endforeach
                </td>
                @endif
                
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/text/'.$text->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'text.destroy', 
                             'args'=>['id' => $text->id]])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $texts->appends($url_args_w)->render() !!}
        @endif
