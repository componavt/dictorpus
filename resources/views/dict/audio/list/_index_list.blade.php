<?php $list_count=1;?>
@if (sizeof($informant->lemmas))
    {!! Form::open(['url' => 'dict/audio/list/'.$informant->id.'/remove',
                    'method' => 'get'])
    !!}
        <table id="lemmasTable" class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>N</th>
                <th><input id="select-all-lemmas" type="checkbox" checked></th>
                <th>{{ trans('dict.lemmas') }}</th>
                <th>{{ trans('dict.interpretation') }}</th>
                <th>{{ trans('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($informant->lemmas->sortBy('lemma') as $lemma)
            <tr id="row-{{$lemma->id}}">
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
                <td data-th="{{ trans('messages.actions') }}">
                    <div id="audios-{{$lemma->id}}" class='audio-button' data-all-audios='0'>
                    </div>
                    <div class='record-button'>                  
                        <i id="record-audio-{{$lemma->id}}" 
                           class="fa fa-microphone record-audio record-stop fa-lg" 
                           data-id="{{$lemma->id}}"></i>
                    </div>
                    <div id="new-audio-{{$lemma->id}}" class="audio-player"></div>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
    @include('widgets.form.formitem._submit', ['title' => trans('messages.delete')])
    {!! Form::close() !!}
@endif
