<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
        @if ($numAll)
        <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lemma') }}</th>
                
                @if(!$url_args['search_lang'])
                <th>{{ trans('dict.lang') }}</th>
                @endif
                
                @if(!$url_args['search_pos'])
                <th>{{ trans('dict.pos') }}</th>
                @endif
                
                <th>{{ trans('dict.interpretation') }}</th>
                <th>{{ trans('dict.wordforms') }}&nbsp;*</th>
                <th>{{ trans('messages.examples') }}&nbsp;**</th>
                @if (User::checkAccess('dict.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
            @foreach($lemmas as $lemma)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.lemma') }}">
                    <a href="{{ LaravelLocalization::localizeURL('/dict/lemma') }}/{{$lemma->id}}{{$args_by_get}}">{!!highlight($lemma->lemma, $url_args['search_w'], 'search-word')!!}</a>
                </td>
                
                @if(!$url_args['search_lang'])
                <td data-th="{{ trans('dict.lang') }}">
                    @if($lemma->lang)
                        {{$lemma->lang->name}}
                    @endif
                </td>
                @endif
                
                @if(!$url_args['search_pos'])
                <td data-th="{{ trans('dict.pos') }}">
                    @if($lemma->pos)
                        {{$lemma->pos->name}}
                        {{$lemma->featsToString()}}
                    @endif
                </td>
                @endif
                
                <td data-th="{{ trans('dict.interpretation') }}">
                    @foreach ($lemma->getMultilangMeaningTexts() as $meaning_string) 
                        {!!highlight($meaning_string, $url_args['search_w'], 'search-word')!!}<br>
                    @endforeach
                </td>
                <td data-th="{{ trans('dict.wordforms') }}">                    
                    @if ($lemma->wordforms && $lemma->wordforms()->count())
                    {{$lemma->wordforms()->whereNotNull('gramset_id')->count()}}
                        @if ($lemma->wordforms()->whereNull('gramset_id')->count())
                        + <span class="unchecked-count">{{$lemma->wordforms()->whereNull('gramset_id')->count()}}</span>
                        @endif
                        <?php  $wordforms = ''; ?>
                        @if ($url_args['search_w'])
                            <?php
                                $wordforms = highlight($lemma->searchWord($url_args['search_w'],3), $url_args['search_w'], 'search-word'); ?>
                        @else 
                            <?php $wordforms = $lemma->wordformsForSearch( 
                                    $url_args['search_gramsets'], 
                                    $url_args['search_dialects'], 
                                    $url_args['search_wordforms']);?>
                        @endif
                        
                        @if ($wordforms) 
                        (<i>{!! $wordforms !!}</i>)
                        @endif
                    @else
                    0
                    @endif
                </td>
                <td data-th="{{ trans('messages.examples') }}">
                    <?php $total_ex = $lemma->countExamples();?>
                    @if ($total_ex)
                        <?php $unchecked = $lemma->countUncheckedExamples();?>
                        {{$lemma->countCheckedExamples()}} /
                        @if ($unchecked >0)
                            <span class="unchecked-count">
                        @endif
                        {{$unchecked}} 
                        @if ($unchecked >0)
                            </span>
                        @endif
                        /
                    @endif
                    {{$lemma->countExamples()}}
                </td>
                @if (User::checkAccess('dict.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit_small_button', 
                             ['route' => '/dict/lemma/'.$lemma->id.'/edit'])
                    @include('widgets.form.button._delete_small_button', ['obj_name' => 'lemma'])
                </td>
                @endif
            </tr>
            @endforeach
        </table>
            {!! $lemmas->appends($url_args)->render() !!}
            
            <p><big>*</big> -  {{ trans('dict.wordform_comment') }}</p>
            <p><big>**</big> -  {{ trans('dict.example_comment') }}</p>
        @endif
