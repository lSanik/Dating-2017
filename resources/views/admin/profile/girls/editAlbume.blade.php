@extends('admin.layout')

@section('styles')

    <link href="{{ url('/assets/css/bootstrap-reset.css') }}" rel="stylesheet">
    <link href="{{ url('/assets/css/fileinput.css') }}" rel="stylesheet">
    <style>
        .kv-file-upload.btn.btn-xs.btn-default{
            display: none!important;
        }
    </style>
    <style>
        .photo{
            float: left;
            display: inline-block;
            width: 30%;
            position: relative;
        }

        .photo .remove {
            color: red;
            padding: 10px;
            width: 100%;
            cursor: pointer;
        }
        img {
            padding: 10px;
            cursor: pointer;
        }


        /* The Modal (background) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            padding-top: 100px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
        }

        /* Modal Content (Image) */
        .modal-content {
            margin: auto;
            display: block;
            width: 60%;
            max-width: 80%;
        }

        @-webkit-keyframes zoom {
            from {-webkit-transform:scale(0)}
            to {-webkit-transform:scale(1)}
        }

        @keyframes zoom {
            from {transform:scale(0)}
            to {transform:scale(1)}
        }

        /* The Close Button */
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }

        /* 100% Image Width on Smaller Screens */
        @media only screen and (max-width: 700px){
            .modal-content {
                width: 100%;
            }
        }
        .delete_gallery{
            position: absolute;
            top: 10px;
            background: red;
            color: white;
            /* right: 25px; */
            width: 25px;
            text-align: center;
            height: 25px;
            font-size: 20px;
            line-height: 25px;
            opacity: 0.7;
            transition: 0.3s;
            left: 0;
            right: 0;
            margin: auto;
        }
        .delete_gallery:hover{
            opacity: 1;
            transition: 0.3s;
            color: white;
        }
    </style>
@stop

@section('content')

    <section class="panel">
        <header class="panel-heading">{{ trans('albums.create') }}</header>
        <div class="panel-body">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
                {!! Form::open(['url' =>url('/'.App::getLocale().'/admin/girl/edit/'.$id.'/edit_album/'.$album->id), 'class' => 'form', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                <div class="form-group">
                    {!! Form::label(trans('albums.name')) !!}
                    <input name="name" type="text" class="form-control" value="{{$album->name}}">
                </div>
                <div class="form-group">
                    {!! Form::label(trans('albums.cover')) !!}
                    {!! Form::file('cover_image', ['class' => 'file','id'=>'cover_image', 'accept' => 'image/*'],"uploads/".$album->cover_image) !!}
                </div>
                <div class="form-group">
                    {!! Form::label(trans('albums.choose')) !!}

                    <input id="input-fcount-1" type="file" name="files[]" multiple="multiple" class="file" accept="image/*">
                </div>
                {!! Form::input('hidden','user_id',$id) !!}
                {!! Form::input('hidden','aid',$album->id) !!}
                <div class="form-group text-center">
                    <input type="submit" class="btn btn-lg btn-success" value="{{ trans('albums.send') }}">
                </div>
                {!! Form::close() !!}
                <div class="col-md-12">
                    @foreach($photos as $p)
                        <div class="photo" id="photo-{{$p->id}}">
                            <img src="{{ url('/uploads/'.$p->image) }}" width="100%">
                            <a class="delete_gallery" href="#" onclick="deleteFoto(event,{{$p->id}});"  ><i class="fa fa-trash-o"></i></a>
                        </div>
                    @endforeach
                </div>
                <div class="btn col-md-12">
                    <a style="width: 100%" class="btn btn-lg btn-success" href="{{url('/'.App::getLocale().'/admin/girl/edit/'.$id)}}">{{ trans('albums.btn-back') }}</a>
                </div>
                <!-- The Modal -->
                <div id="myModal" class="modal" onclick="document.getElementById('myModal').style.display='none'">

                    <!-- The Close Button -->
                    <span class="close" onclick="document.getElementById('myModal').style.display='none'">&times;</span>

                    <!-- Modal Content (The Image) -->
                    <img class="modal-content" id="img01">
                </div>
        </div>
    </section>
@stop
@section('scripts')


    <script type="text/javascript" src="{{ url('/assets/js/bootstrap-fileinput-master/js/fileinput.js') }}"></script>
    <script type="text/javascript" src="{{ url('/assets/js/file-input-init.js') }}"></script>
    <script>
        $("#cover_image").fileinput({
            minFileCount: 1,
            maxFileCount: 1,
            validateInitialCount: true,
            overwriteInitial: true,
            initialPreview: [
                "<img style='height:160px' src='/uploads/{{$album->cover_image}}'>",
            ],
            allowedFileExtensions: ["jpg", "png", "gif"]
        });

        $("#input-fcount-1").fileinput({
            uploadUrl: "/file-upload-batch/1",

            uploadAsync: false,
            minFileCount: 1,
            maxFileCount: 10,
            overwriteInitial: false,
            allowedFileExtensions: ["jpg", "png", "gif"],
            showUpload: false,
        });
    </script>
    <script>
        $(function(){
            // Get the modal
            var modal = document.getElementById('myModal');

            // Get the image and insert it inside the modal - use its "alt" text as a caption
            var modalImg = document.getElementById("img01");

            $('img').click(function(){
                modal.style.display = "block";
                modalImg.src = $(this).attr('src');
                $('.navbar-static-top').css('z-index', '0');
            });

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            }

        });
        $(document).ready(function(){
            $('.remove').click(function(){

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: '{{ url('remove/image') }}',
                    data: {id : $(this).data('id')},
                    success: function( response ){
                        console.log(response);
                        $(this).parent().remove();
                    },
                    error: function( response ){
                        console.log(response);
                    }
                })
            });
        });
        function deleteFoto(event,foID){
            event.preventDefault();
            $.post( "/admin/girl/dropImageAlbum/"+foID, {_token : $('input[name="_token').val()} ).done( function( data ) {
                $("#photo-"+foID).remove();
            });
        }
    </script>
@stop