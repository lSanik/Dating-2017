@extends('admin.layout')

@section('styles')

    <link href="{{ url('/assets/css/bootstrap-reset.css') }}" rel="stylesheet">
    <link href="{{ url('/assets/css/fileinput.css') }}" rel="stylesheet">
    <style>
        .kv-file-upload.btn.btn-xs.btn-default{
            display: none!important;
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
            {!! Form::open(['url' =>url('/'.App::getLocale().'/admin/girl/edit/'.$user_id.'/add_album'), 'class' => 'form', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                <div class="form-group">
                    {!! Form::label(trans('albums.name')) !!}
                    <input name="name" type="text" class="form-control">
                </div>
                <div class="form-group">
                    {!! Form::label(trans('albums.cover')) !!}
                    {!! Form::file('cover_image', ['class' => 'file', 'accept' => 'image/*']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label(trans('albums.choose')) !!}

                    <input id="input-fcount-1" type="file" name="files[]" multiple="multiple" class="file" accept="image/*">
                </div>
                    {!! Form::input('hidden','user_id',$user_id) !!}
                <div class="form-group text-center">
                    <input type="submit" class="btn btn-lg btn-success" value="{{ trans('albums.send') }}">
                </div>
            {!! Form::close() !!}
        </div>
    </section>
@stop
@section('scripts')

    <script type="text/javascript" src="{{ url('/assets/js/bootstrap-fileinput-master/js/fileinput.js') }}"></script>
    <script type="text/javascript" src="{{ url('/assets/js/file-input-init.js') }}"></script>
    <script>
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
@stop