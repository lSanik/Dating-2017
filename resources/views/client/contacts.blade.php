@extends('client.app')

@section('styles')
    <style>
        .margin-bottom-10{
            margin-bottom: 10px;
        }
        .grg-table-header > th{
            background-color: #EBEBEB;
        }
        .grg-modal{
            padding:0 !important;
        }
        .grg-modal-content{
            text-align: left !important;
        }
        .grg-ticket-closed{
            background-color: #DDDDDD;
        }
        .grg-form-button{
            vertical-align: bottom;
            margin-left: 8px;
        }

        /* Ticket Edit */
        .messages {
            margin-top: 30px;
            height: 30vh;
            overflow-y: scroll;
            padding: 15px;
            border: 1px solid #ccc;
            background: #ffffff;
        }

        .message{
            width: 100%;
            word-break: break-word;
            margin-top: 5px;
            margin-bottom: 5px;
            padding: 5px;
        }

        .message-support{
            width: 100%;
            word-break: break-word;
            margin-top: 5px;
            margin-bottom: 5px;
            padding: 5px;
            border: 1px solid #ccc;
            background-color: #ebebeb;
            clear: both;
            text-align: right;
        }
        .photo{
            height: 32px;
            width: 32px;
            float: left;
            line-height: 48px;
            margin: 5px;
        }
        .photo img{
            width: 32px;
            height: 32px;
            display: block;
            margin: 0 auto;
        }
        .message-support .photo{
            float: right;
        }

    </style>
@stop

@section('content')
<div class="container">
    <div class="row col-md-10 col-md-offset-1">
        <h1>{{ trans('contacts.heading') }}</h1>
        <div class="col-md-6">
            <h4>{{ trans('contacts.left_h') }}</h4>
            <p style="font-weight: 700;">{{ trans('contacts.address') }}</p>
            <p>{{ trans('contacts.office_address') }}</p>

            <p style="font-weight: 700;">{{ trans('contacts.email') }}</p>
            <p><a href="mailto:{{trans('contacts.office_email')}}">{{ trans('contacts.office_email') }}</a></p>

            <p style="font-weight: 700;">{{ trans('contacts.phone') }}</p>
            <p>{{ trans('contacts.office_phone') }}</p>
        </div>
        <div class="col-md-6">

            @if( Session::has('success_message') )
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ Session::get('success_message') }}
                        </div>
                    </div>
                </div>
            @endif

            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!Auth::user())
            <h4>{{ trans('contacts.right_h') }}</h4>

            {!! Form::open(['url' => '/contacts/message', 'method' => 'POST']) !!}
                <div class="form-group">
                    {!! Form::label('email', trans('contacts.email')) !!}
                    {!! Form::input('email','email', null, ['class' => 'form-control', 'required' => 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('subject', trans('contacts.subject')) !!}
                    {!! Form::input('subject','subject', null, ['class' => 'form-control', 'required' => 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('message', trans('contacts.message')) !!}
                    <textarea name="message" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    {!! Form::submit(trans('contacts.submit'), ['class' => 'btn btn-pink']) !!}
                </div>
                {!! Form::close() !!}
            @else
                <h4>{{ trans('contacts.right_h_support') }}</h4>
                <div class="button">
                    <button type="button" class="btn btn-pink" id="button-ticket" data-toggle="modal" data-target="#ticketModal">{{ trans('contacts.new_request') }}&nbsp;
                        <i class="fa fa-plus-circle"></i>
                    </button>
                </div>
                <div class="margin-bottom-10"></div>
                    <table class="table table-inbox table-hover">
                        <thead>
                        <tr class="grg-table-header">
                            <th>ID</th>
                            <th>{{ trans('contacts.subject') }}</th>
                            <th>{{ trans('contacts.status') }}</th>
                            <th>{{ trans('contacts.date') }}</th>
                        </tr>

                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                                <tr id="edit-ticket" data-toggle="modal" data-target="#ticketEditModal-{{$ticket->id}}"
                                    @if($ticket->name == 'closed')
                                        class="grg-ticket-closed"
                                    @endif
                                >
                                    <td>{{ $ticket->id }}</td>
                                    <td>{{ $ticket->subject }}</td>
                                    <td>{{ trans('contacts.'.$ticket->name) }}</td>
                                    <td>{{ date('Y-m-d H:i', strtotime($ticket->updated_at)) }}</td>
                                </tr>
                                @include('client.tickets.edit')
                            @endforeach
                        </tbody>
                    </table>
                {!! $tickets->render() !!}
                @include('client.tickets.add')
            @endif
        </div>
    </div>
</div>

@stop

@section('scripts')

@stop