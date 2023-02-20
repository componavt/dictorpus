<?php $list_count=1;?>
@if (sizeof($lemmas))
    {!! Form::open(['url' => 'dict/audio/list/'.$informant->id.'/add',
                    'method' => 'get'])
    !!}
        <table id="lemmasTable" class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>N</th>
                <th><input id="select-all-lemmas" type="checkbox" checked></th>
                <th>{{ trans('dict.lemmas') }}</th>
                <th>{{ trans('dict.interpretation') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lemmas as $lemma)
            <tr>
                <td>{{$list_count++}}</td>
                <td><input class="choose-lemma" type="checkbox" name="checked_lemmas[]" value="{{$lemma->id}}" checked></td>
                <td data-th="{{ trans('dict.lemmas') }}">
                    <a href="{{ LaravelLocalization::localizeURL('dict/lemma/'.$lemma->id) }}">
                        {{$lemma->lemma}}<br>
                    </a>
                </td>
                <td>
                    @foreach ($lemma->getMultilangMeaningTexts() as $meaning_string) 
                        {{$meaning_string}}<br>
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
    
    @include('widgets.form.formitem._submit', ['title' => trans('dict.add_to_list')])
    {!! Form::hidden('search_dialect', $url_args['search_dialect']) !!}            
    {!! Form::close() !!}
@endif
