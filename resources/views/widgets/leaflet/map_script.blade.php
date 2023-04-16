<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
     integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
     crossorigin=""></script>
    
<script>
    var mymap = L.map('mapid').setView([62, 35], 7);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
//    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
            maxZoom: 18,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
/*            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, ' +
                    'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',*/
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1
    }).addTo(mymap);

@foreach ($places as $place)
    L.marker([{{$place['latitude']}}, {{$place['longitude']}}], {icon: L.divIcon({className: 'marker-icon marker-{{$place["color"]}}'})})
            .bindPopup('{!!$place["popup"]!!}').addTo(mymap).openPopup();
@endforeach
</script>
