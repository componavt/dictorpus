    <div id="letter-b">
        <div id="letter-links">
        @foreach ($alphabet as $letter)
        <a class="{{$url_args['search_letter'] == $letter ? 'letter-active' : '' }}" 
            href="{{ LaravelLocalization::localizeURL('/ldl?search_letter='.$letter)}}">{{$letter}}</a>
        @endforeach
        </div>        
    </div>
