@extends('layouts.master')

@section('title')
{{ trans('main.site_title') }}

<? $total_lemmas = 1313;
@endsection

@section('content')
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('navigation.about_project') }}</div>

                <div class="panel-body">
                    {!! trans('blob.welcome_text',[
                        'total_dialects'=>$total_dialects,
                        'total_lemmas'=>$total_lemmas,
                        'lemmas' => $lemmas_choice,
                        'texts' => $texts_choice,
                        'total_texts'=>$total_texts]) !!}
                    
                    <div id="last-created-lemmas" class="block-list">
                    </div>
                    
                    <div id="last-updated-lemmas" class="block-list">
                    </div>
                    
                    <div id="last-created-texts" class="block-list">
                    </div>
                    
                    <div id="last-updated-texts" class="block-list">
                    </div>
                </div>
            </div>
@endsection

@section('footScriptExtra')
    {!!Html::script('js/new_list_load.js')!!}
@stop

@section('jqueryFunc')
    newListLoad('/dict/lemma/new_list/', 'last-created-lemmas',{{$limit}});
    newListLoad('/dict/lemma/updated_list/', 'last-updated-lemmas',{{$limit}});
    newListLoad('/corpus/text/new_list/', 'last-created-texts',{{$limit}});
    newListLoad('/corpus/text/updated_list/', 'last-updated-texts',{{$limit}});
@stop
