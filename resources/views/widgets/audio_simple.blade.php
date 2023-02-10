<!--div class="essential_audio circle" data-url="{{$route}}"></div-->
<audio class="simple" controls {{isset($autoplay) && $autoplay ? 'autoplay' : ''}}>
    <source  src="{{$route}}">
</audio>

