@section('style')

@stop
@extends('admin.layout')


@section('content')

    <section class="panel">
        <heading>

        </heading>
        <div class="panel-body">
            <table class="table table-hovered">
                <thead>
                <th>ID</th>
                <th>{{trans('/admin/index.name')}}/{{trans('/admin/index.surname')}}</th>
                <th>{{trans('/admin/index.avatar')}}</th>
                @if( Auth::user()->hasRole('Owner') )
                    <th>{{trans('/admin/index.partner')}}</th>
                @endif
                <th>{{trans('/admin/index.online')}}</th>
                <th>{{trans('/admin/index.webCam')}}</th>
                <th>Hot block</th>
                <th>{{trans('/admin/index.lastEntrance')}}</th>
                <th><i class="fa fa-cogs"></i> {{trans('/admin/index.control')}}</th>
                </thead>
                <tbody>
                @foreach($girls as $girl)
                    <tr>
                        <td>{{ $girl->id }}</td>
                        <td>{{ $girl->first_name }} {{ $girl->last_name }}</td>
                        <td><img width="150px" src="{{ url('uploads/'.$girl->avatar)}}"></td>
                        @if( Auth::user()->hasRole('Owner') )
                            <td>{{ $girl->partner_id }}</td> <!-- Уточнить что выводить -->
                        @endif

                        <td>
                            @if($girl->isOnline())
                               <button class="btn btn-small online_btn"> Online </button>
                            @else
                                <span class="red">{{ trans('admin.No') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($girl->webcam !== 0)
                                <span class="green">{{ trans('admin.yes') }}</span>
                            @else
                                <span class="red">{{ trans('admin.No') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($girl->hot !== 0)
                                <span class="green">{{ trans('admin.yes') }}</span>
                            @else
                                <span class="red">{{ trans('admin.No') }}</span>
                            @endif
                        </td>
                        <td> {{ $girl->last_login }} </td>
                        <td>
                            <a target="_blank" href="{{ url(App::getLocale().'/profile/show/'.$girl->id) }}" class="btn btn-success btn-xs"><i class="fa fa-check"></i></a>
                            @if( Auth::user()->hasRole('Owner') )
                                <a id="button-migrate-user" onclick="ChangeOpen('{{$girl->id}}','{{$girl->partner_id}}');" data-toggle="modal" data-target="#migrate-user" href="#" class="btn btn-warning btn-xs"><i class="fa fa-arrows-h"></i></a>
                            @endif
                            <a href="{{ url(App::getLocale().'/admin/girl/edit/'.$girl->id) }}" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>
                            <a href="{{ url(App::getLocale().'/admin/girl/drop/'.$girl->id) }}" class="btn btn-danger btn-xs" ><i class="fa fa-trash-o "></i></a>
                            <a data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('/admin/index.sender')}}" href="{{ url(App::getLocale().'/admin/sender/new/'.$girl->id) }}" class="btn btn-primary btn-xs"><i class="fa fa-comment-o"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
    <div class="modal fade in" id="migrate-user" tabindex="-1" role="dialog" aria-labelledby="migrate-user" style="display: none;">
        <style>
            .modal-body {
                width: 100%;
                display: inline-block;
                padding: 0!important;
            }
            .modalContent {
                background: url(/assets/img/patterns/gray_pattern.gif);
                border: 10px solid #fafafa;
                padding: 15px;
            }
        </style>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modalContent col-md-12">
                        <div class="col-md-12" style="margin-bottom: 15px">
                            <div class="pull-right">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            </div>
                            <h4 class="pull-left"><i class="fa fa-magic"></i>Change partner</h4>
                        </div>
                        {!! Form::open(['url' => '/admin/girl/changepartner', 'class' => 'form', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                            <input type="hidden" name="girl_id" value="">

                            <div class="form-group col-md-12">
                                <label for="first_name">Current partner</label>
                                <select type="text" name="partner_list" class="form-control" placeholder="First Name" required="">
                                        <option value="1">Administrator</option>
                                    @foreach($partners as $parnter)
                                        <option value="{{$parnter->id}}">{{'('.$parnter->id.') '.$parnter->first_name.' '.$parnter->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-12" style="    text-align: center;">
                                <button class="btn btn-pink btn-sm" type="submit" id="createAccount">Change partner</button>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function ChangeOpen(girl_id,partner_id) {
            if(partner_id==0){
                $("select[name='partner_list']").val(1).change();
            }
            $("input[name='girl_id']").val(girl_id);
            $("select[name='partner_list']").val(partner_id).change();
        }
    </script>
@stop
@section('scripts')

@stop