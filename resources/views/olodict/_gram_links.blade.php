        @foreach ($gram_list as $gram)
        <a class="{{$url_args['search_gram'] == $gram->gram ? 'gram-active' : '' }}" onClick="viewGram(this)">{{$gram->gram}}</a>
        @endforeach
