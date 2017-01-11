@extends('admin.layout')

@section('styles')

    <link href="{{ url('/assets/css/bootstrap-reset.css') }}" rel="stylesheet">
    <link href="{{ url('/assets/css/fileinput.css') }}" rel="stylesheet">
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
            {!! Form::open(['url' => '/admin/blog/new', 'class' => 'form', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
            <div class="col-md-9">
                <div class="form-group col-md-12">
                    <label for="title">{{ trans('pages.title') }}</label>
                    {!! Form::input('text', 'title', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-12 form-group">
                    <label for="body">{{ trans('pages.body') }}</label>
                    <textarea name="body" class="summernote"></textarea>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="image">{{ trans('pages.image') }}</label>
                    <input type="file" name="image" accept="image/*" id="image">
                </div>
                <div class="form-group text-center">
                    {{ Form::submit(trans('pages.submit'), ['class' => 'btn btn-success']) }}
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </section>
@stop
@section('scripts')
    <script type="text/javascript" src="{{ url('/assets/js/bootstrap-fileinput-master/js/fileinput.js') }}"></script>
    <script type="text/javascript" src="{{ url('/assets/js/file-input-init.js') }}"></script>
@stop