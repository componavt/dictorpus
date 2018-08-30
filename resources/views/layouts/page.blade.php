@extends('layouts.master')

@section('title')
@yield('page_title')
@endsection

@section('content')
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1>@yield('page_title')</h1>

                    @yield('body')
                </div>
            </div>
@endsection
