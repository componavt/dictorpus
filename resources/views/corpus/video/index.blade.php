<?php $list_count = 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.video') }}
@stop

@section('body')
    <div class="row">
        <?php $count=1; ?>
        @foreach($videos as $video)
        <div class="col-sm-6">
            @include('widgets.youtube',
                    ['width' => '100%',
                     'height' => '270',
                     'video' => $video->youtube_id
                    ])
            <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$video->text_id) }}">{{$video->text->title}}</a></p>        
        </div>
        @endforeach
        </div>
    </div>
@stop


