@extends('layouts.page')

@section('page_title')
{{ trans('navigation.publications') }}
@endsection

@section('body')
    {!! trans('blob.our_publications') !!}<br>

    <div class="row" style='margin-bottom: 20px'>
        <div class="col-sm-4">
            @include('widgets.youtube',
                    ['width' => '100%',
                     'height' => '270',
                     'video' => 'dN_o4rgpTbQ'
                    ])
        </div>
        <div class="col-sm-4">
            @include('widgets.youtube',
                    ['width' => '100%',
                     'height' => '270',
                     'video' => 'cUpqM97LXGs'
                    ])
        </div>
        <div class="col-sm-4">
            @include('widgets.youtube',
                    ['width' => '100%',
                     'height' => '270',
                     'video' => '0coYBYlJmKY'
                    ])
        </div>
    </div>
    
    
    <h2>{{ trans('navigation.publications_about')}}</h2>
{!! trans('blob.publications_about') !!}

    <div class="row">
        <div class="col-sm-4">
            @include('widgets.youtube',
                    ['width' => '100%',
                     'height' => '270',
                     'video' => 'rDTEKEQd7YI'
                    ])
        </div>
        <div class="col-sm-4">
            @include('widgets.youtube',
                    ['width' => '100%',
                     'height' => '270',
                     'video' => '3DLsfO-c1Hc'
                    ])
        </div>
    </div>
</div>
@endsection
