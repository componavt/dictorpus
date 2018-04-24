@extends('layouts.master')

@section('title')
{{ trans('navigation.stats') }}
@endsection

@section('content')
            <div class="panel panel-default">
                <div class="panel-heading">{{trans('navigation.stats')}}</div>

                <div class="panel-body">
                    <table>
                        <tr>
                            <th colspan='2'>{{trans('stats.stats_by_dict')}}</th>
                        </tr>
                        <tr>
                            <td>{{trans('stats.of_lemmas')}}</td><td>{{$total_lemmas}}</td>
                        </tr>
                        <tr>
                            <th colspan='2'>{{trans('stats.stats_by_corp')}}</th>
                        </tr>
                        <tr>
                            <td>{{trans('stats.of_texts')}}</td><td>{{$total_texts}}</td>
                        </tr>
                    </table>
                </div>
            </div>
@endsection
