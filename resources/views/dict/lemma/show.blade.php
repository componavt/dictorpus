@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>

        @if (User::checkAccess('dict.edit'))
            | @include('widgets.form.button._delete', 
                       ['route' => 'lemma.destroy', 
                        'args'=>['id' => $lemma->id]]) 
            | <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}{{$args_by_get}}">{{ trans('messages.create_new_f') }}</a>
        @else
            | {{ trans('messages.edit') }} | {{ trans('messages.delete') }}
        @endif

            | <a href="/dict/lemma/{{ $lemma->id }}/history{{$args_by_get}}">{{ trans('messages.history') }}</a>
        </p>

        <div class="row">
            <div class="col-sm-8">
                <h2>
                    {{ $lemma->lemma }}
                    @if (User::checkAccess('dict.edit'))
                        @include('widgets.form.button._edit', 
                                 ['route' => '/dict/lemma/'.$lemma->id.'/edit',
                                  'without_text' => 1])
                    @endif
                </h2>
            </div>
            <div class="col-sm-4" style="text-align: right; font-weight: bold">   
                <span id="lemmaStemAffix">{{$lemma->stemAffixForm()}}</span>
                <img class="img-loading" id="img-loading_stem-affix" src="{{ asset('images/loading.gif') }}">
                @include('widgets.form.button._reload', 
                         ['data_reload' => $lemma->id,
                          'class' => 'reload-stem-affix-by-wordforms',
                          'func' => 'reloadStemAffixByWordforms',
                          'title' => trans('dict.reload-stem-affix-by-wordforms')])
            </div>
        </div>
        
        <p><b>{{ trans('dict.lang') }}:</b> {{ $lemma->lang->name}}</p>
        @if ($lemma->pos)
        <p>
            <b>{{ trans('dict.pos') }}:</b> {{ $lemma->pos->name}}  
            @include('dict.lemma.show.features')
        </p>
        @endif
        @if ($lemma->variantsWithLink())
        <p>
            <b>{{trans('dict.variants')}}:</b> {!!$lemma->variantsWithLink()!!}
        </p>
        @endif
        @if ($lemma->phraseLemmasListWithLink())
        <p>
            <b>{{trans('dict.phrase_lemmas')}}:</b> {!!$lemma->phraseLemmasListWithLink()!!}
        </p>
        @endif

        @if (sizeof($lemma->phrases))
        <p>
            <b>{{trans('dict.phrases')}}:</b> 
            @if (User::checkAccess('dict.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}{{$args_by_get}}{{$args_by_get?'&':'?'}}pos_id={{ 
                        \App\Models\Dict\PartOfSpeech::getPhraseID()}}&phrase_lemmas[{{$lemma->id}}]={{$lemma->lemma}}">
                <i class="fa fa-plus fa-lg add-phrase" title="{{trans('dict.add-phrase')}}"></i>
            </a>
            @endif
            
            @foreach ($lemma->phrases->sortBy('lemma') as $ph_lemma) 
            <br><a href="{{LaravelLocalization::localizeURL('/dict/lemma/'.$ph_lemma->id)}}">{{$ph_lemma->lemma}}</a> 
                - {{$ph_lemma->phraseMeaning()}}
            @endforeach

        </p>
        @endif
        
        @if ($lemma->omonymsListWithLink())
        <p>
            <b{!! User::checkAccess('dict.edit')?' class="warning"':'' !!}>{{trans('dict.omonyms')}}:</b> {!!$lemma->omonymsListWithLink()!!}
        </p>
        @endif

        @foreach ($lemma->meanings as $meaning)
        <table class="table lemma-meaning">
            <tr>
                <td>
                    <h3>{{$meaning->meaning_n}}  {{ trans('dict.meaning') }}</h3>

                    @include('dict.lemma.show.meaning_texts')

                    @include('dict.lemma.show.meaning_relations')

                    @include('dict.lemma.show.meaning_translations')
                </td>
                <td>
                    <img class="img-loading" id="img-loading_{{$meaning->id}}" src="{{ asset('images/loading.gif') }}">
                    <div  id="meaning-examples_{{$meaning->id}}">
{{--                    @include('dict.lemma.show.examples') --}}
                    </div>
                </td>
            </tr>
        </table>
        @endforeach

        @if ($lemma->isChangeable())
            @include('dict.lemma_wordform._show')
        @endif
            
        @include('dict.lemma._modal_delete')
<?php $route_for_load = ($update_text_links) ? '/dict/meaning/examples/reload' : '/dict/meaning/examples/load'; ?>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/lemma.js')!!}
    {!!Html::script('js/meaning.js')!!}
    {!!Html::script('js/wordform.js')!!}
@stop

@section('jqueryFunc')
    loadWordforms({{$lemma->id}});
    @foreach ($lemma->meanings as $meaning)
        loadExamples('{{LaravelLocalization::localizeURL($route_for_load)}}', {{$meaning->id}});
    @endforeach
    
    chooseDialectForGenerate({{$lemma->id}});
    recDelete('{{ trans('messages.confirm_delete') }}');
/*    toggleExamples();
    addExample('{{LaravelLocalization::localizeURL('/dict/meaning/example/add')}}'); 
    removeExample('{{LaravelLocalization::localizeURL('/dict/lemma/remove/example')}}');
    reloadExamples('{{LaravelLocalization::localizeURL('/dict/meaning/examples/reload')}}'); */
@stop

