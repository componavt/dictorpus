        <div class="col-sm-4">
        @if (isset($rutube_id))        
            @include('widgets.rutube',
                    ['width' => '100%',
                     'height' => '270',
                     'video' => $rutube_id
                    ])
        @elseif (isset($youtube_id))        
            @include('widgets.youtube',
                    ['width' => '100%',
                     'height' => '270',
                     'video' => $youtube_id
                    ])
        @endif
        </div>
