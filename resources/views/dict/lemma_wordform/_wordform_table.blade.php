    @if ($lemma->wordforms()->count())
        <?php $key=1; ?>
        <table class="table-bordered table-striped">
            <tr>
                <th>No</th>
                <th>{{ trans('dict.gramsets') }}</th>
                @foreach ($lemma->existDialects() as $dialect_id=>$dialect_name)
                <th>
                    {{$dialect_name}} ({{$lemma->wordforms()->wherePivot('dialect_id',$dialect_id)->count()}})
                    @if (User::checkAccess('dict.edit'))
                        @include('widgets.form.button._edit', 
                                 ['route' => '/dict/lemma_wordform/'.$lemma->id.'/edit',
                                  'args_by_get' => (isset($args_by_get) && $args_by_get) 
                                                    ? $args_by_get.'&dialect_id='.$dialect_id 
                                                    : '?dialect_id='.$dialect_id,
                                  'without_text' => 1])
                        <i data-reload="{{$lemma->id.'_'.$dialect_id}}" class="fa fa-sync-alt fa-lg reload-wordforms" 
                           title="{{trans('messages.reload')}}" onClick="reloadWordforms(this, '', [{{$lemma->meaningIdsToList()}}])"></i>
                        <a style="cursor: pointer" onClick="deleteWordforms('{{$lemma->id.'_'.(!$dialect_id? 'NULL' : $dialect_id)}}', [{{$lemma->meaningIdsToList()}}])"><i class="fa fa-trash fa-lg"></i></a>
                    @endif

                    </th>
                @endforeach
            </tr>
            
            @foreach ($lemma->existGramsetsGrouped() as $category_name => $category_gramsets)
            <tr>
                <td></td>
                <td colspan="2"><b><big>{{$category_name}}</big></b></td>
            </tr>
                @if($category_gramsets)
                    @foreach ($category_gramsets as $gramset_id => $gramset_name)
            <tr>
                <td>{{$key++}}.</td>
                <td>
                    {{$gramset_name}}
                </td>
                        @foreach (array_keys($lemma->existDialects()) as $dialect_id)
                <td>
                    {!!highlight($lemma->wordform($gramset_id, $dialect_id, 1),$search_w ?? null, 'search-word')!!}
                </td>
                        @endforeach
            </tr>
                    @endforeach
                @endif
            @endforeach
        </table>
        @endif
