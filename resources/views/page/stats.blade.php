@extends('layouts.page')

@section('page_title')
{{ trans('navigation.stats') }}
@endsection

@section('headExtra')
    {!!Html::style('css/stats.css')!!}
@stop

@section('body')
                    <table class="table-bordered stats-table">
                        <tr>
                            <th colspan='2'>{{trans('stats.stats_by_dict')}}</th>
                        </tr>
                        <tr>
                            <td>{{trans('navigation.lemmas')}}</td><td><a href="/dict/lemma">{{$total_lemmas}}</a></td>
                        </tr>
                        <tr>
                            <td>{{trans('navigation.wordforms')}}</td><td><a href="/dict/wordform">{{$total_wordforms}}</a></td>
                        </tr>
                        <tr>
                            <td>{{trans('stats.meanings')}}</td><td>{{$total_meanings}}</td>
                        </tr>
                        <tr>
                            <td>{{trans('stats.translations')}}</td><td>{{$total_translations}}</td>
                        </tr>
                        <tr>
                            <td>{{trans('stats.relations')}}</td><td><a href="/dict/lemma/relation">{{$total_relations}}</a></td>
                        </tr>
                        
                        <tr>
                            <th colspan='2'>{{trans('stats.stats_by_words')}}</th>
                        </tr>
                        <tr>
                            <td>{{trans('stats.total_words')}}</td><td>{{$total_words}}</td>
                        </tr>
                        <tr>
                            <td>{{trans('stats.total_checked_words')}}</td><td>{{$total_checked_words}}</td>
                        </tr>
                        <tr>
                            <td>{{trans('stats.all_words_to_checked')}}</td><td>{{$all_words_to_checked}}</td>
                        </tr>
                        
                        <tr>
                            <th colspan='2'>{{trans('stats.stats_by_examples')}}</th>
                        </tr>
                        <tr>
                            <td>{{trans('stats.total_examples')}}</td><td>{{$total_examples}}</td>
                        </tr>
                        <tr>
                            <td>{{trans('stats.total_checked_examples')}}</td><td>{{$total_checked_examples}}</td>
                        </tr>
                        <tr>
                            <td>{{trans('stats.all_examples_to_checked')}}</td><td>{{$all_examples_to_checked}}</td>
                        </tr>
                        
                        <tr>
                            <th colspan='2'>{{trans('stats.stats_by_corp')}}</th>
                        </tr>
                        <tr>
                            <td>{{trans('navigation.texts')}}</td><td><a href="/corpus/text">{{$total_texts}}</a></td>
                        </tr>
                        <tr>
                            <td>{{trans('navigation.informants')}}</td><td><a href="/corpus/informant">{{$total_informants}}</a></td>
                        </tr>
                        <tr>
                            <td>{{trans('navigation.places')}}</td><td><a href="/corpus/place">{{$total_places}}</a></td>
                        </tr>
                        <tr>
                            <td>{{trans('navigation.recorders')}}</td><td><a href="/corpus/recorder">{{$total_recorders}}</a></td>
                        </tr>
                        
                        <tr>
                            <th colspan='2'>{{trans('stats.stats_by_users')}}</th>
                        </tr>
                        <tr>
                            <td>{{trans('stats.total_users')}}</td><td>{{$total_users}}</td>
                        </tr>
                        <tr>
                            <td>{{trans('stats.total_active_editors')}}</td><td>{{$total_active_editors}}</td>
                        </tr>
                    </table>
@endsection
