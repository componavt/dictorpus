        <h3>
            {{ trans('dict.wordforms') }}
            @if (User::checkAccess('dict.edit'))
                {!! Form::open(['url' => '/dict/lemma/'.$lemma->id.'/edit/wordforms',
                                'method' => 'get'])
                !!}
                @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
            <div class="row">
                <div class="col-sm-3">
                @include('widgets.form.formitem._select',
                        ['name' => 'dialect_id',
                         'values' =>$dialect_values,
                         ]) 
                </div>
                <div class="col-sm-1">
                @include('widgets.form.formitem._submit', ['title' => trans('messages.edit')])
                </div>
            </div>                 
                {!! Form::close() !!}
            @endif
        </h3>
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
                                 ['route' => '/dict/lemma/'.$lemma->id.'/edit/wordforms',
                                  'args_by_get' => (isset($args_by_get) && $args_by_get) 
                                                    ? $args_by_get.'&dialect_id='.$dialect_id 
                                                    : '?dialect_id='.$dialect_id,
                                  'without_text' => 1])
                    @endif

                    </th>
                @endforeach
            </tr>
            @foreach ($lemma->existGramsets() as $gramset_id=>$gramset_name)
            <tr>
                <td>{{$key++}}.</td>
                <td>
                    {{$gramset_name}}
                </td>
                @foreach (array_keys($lemma->existDialects()) as $dialect_id)
                <td>{{ $lemma->wordform($gramset_id,$dialect_id) }}</td>
                @endforeach
            </tr>
            @endforeach
        </table>
        @endif
