        @if ($lemma->wordforms()->count())
        <?php $key=1; ?>
        <table class="table-bordered table-striped">
            <tr>
                <th>No</th>
                <th>{{ trans('dict.gramsets') }}</th>
                @foreach ($lemma->existDialects() as $dialect_id=>$dialect_name)
                <th>
                    {{$dialect_name}}
                    @if (User::checkAccess('dict.edit'))
                        @include('widgets.form.button._edit', 
                                 ['route' => '/dict/lemma_wordform/'.$lemma->id.'/edit',
                                  'args_by_get' => (isset($args_by_get) && $args_by_get) 
                                                    ? $args_by_get.'&dialect_id='.$dialect_id 
                                                    : '?dialect_id='.$dialect_id,
                                  'without_text' => 1])
{{--                        @include('widgets.form.button._delete', 
                                   ['route' => 'lemma_wordform.destroy', 
                                    'without_text' => true,
                                    'class' => 'delete-wordforms',
                                    'title' => trans('dict.check_delete_wordforms'),
                                    'args'=>['id' => $lemma->id, 'dialect_id'=>$dialect_id]]) --}}
                        @include('widgets.form.button._reload', 
                                 ['data_reload' => $lemma->id.'_'.$dialect_id,
                                  'class' => 'reload-wordforms',
                                  'func' => 'reloadWordforms',
                                  'title' => trans('messages.reload')])
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
                    <?php print $lemma->wordform($gramset_id, $dialect_id, 1);?>
                </td>
                        @endforeach
            </tr>
                    @endforeach
                @endif
            @endforeach
        </table>
        @endif