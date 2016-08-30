@extends('layouts.master')

@section('title')
{{ trans('dict.pos_list') }}
@stop

@section('content')
        <h2>{{ trans('dict.gram_list') }}</h2>
        
        <table class="table">
        <tbody>
            <tr>
            @foreach($grams as $name => $grams_list)
                <td>
                    <h3>{{ $name }}</h3>
                    @foreach($grams_list as $gramzik)
                        <p>{{ $gramzik->name }} ({{ $gramzik->name_short }})</p>
                    @endforeach
                </td>
            @endforeach
            </tr>
        </tbody>
        </table>
@stop


