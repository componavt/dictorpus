@extends('layouts.master')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h3><i class="fa fa-image"></i> Ajax Image Upload</h3>
                <br>

                <h4>User Profile</h4>
                <hr>
                <div style="display: flex;">
                    <div>
                        <img class="imgPreview img img-circle" 
                         width="80" src="https://via.placeholder.com/80">
                    </div>
                    <div style="margin-left: 15px; flex-grow: 1">
                        <p>Choose a file</p>
                        <input id="photo" type="file">
                        <input type="hidden" name="id" value="{{$user->id}}">
                        <br>
                        <div class="progress">
                            <div class="progress-bar" 
                                 role="progressbar" aria-valuemin="0"
                                 aria-valuemax="100">

                            </div>
                        </div>
                    </div>
                </div>


                <table class="table table-condensed table-bordered">
                    <tr>
                        <td width="100">Name</td>
                        <td>{{$user->name}}</td>
                    </tr>
                    <tr>
                        <td>E-Mail</td>
                        <td>{{$user->email}}</td>
                    </tr>
                    <tr>
                        <td>Phone</td>
                        <td>{{$user->phone}}</td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td>{{$user->address}}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@stop

@section('jqueryFunc')
            $.ajaxSetup({
                headers: {'X-CSRF-Token': '{{csrf_token()}}'}
            });

            var id = $('input[name="id"]').val();


            $('#photo').change(function () {
                var photo = $(this)[0].files[0];
                var formData = new FormData();
                formData.append('id', id);
                formData.append('photo', photo);

                $.ajax({
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                percentComplete = parseInt(percentComplete * 100);
                                console.log(percentComplete);
                                $('.progress-bar').css('width', percentComplete + '%');
                                if (percentComplete === 100) {
                                    console.log('completed 100%')

                                    var imageUrl = window.URL.createObjectURL(photo)
                                    $('.imgPreview').attr('src', imageUrl);
                                    setTimeout(function () {
                                        $('.progress-bar').css('width', '0%');
                                    }, 2000)
                                }
                            }
                        }, false);
                        return xhr;
                    },
                    url: '{{route('updateProfile')}}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
console.log(res)                    
                        if(!res.success){alert(res.error)}
                    }
                })
            });
@endsection