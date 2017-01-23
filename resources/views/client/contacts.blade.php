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
            @if(!Auth::user())
            <h4>{{ trans('contacts.right_h') }}</h4>

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
                                <tr>
                                    <td>{{ $ticket->id }}</td>
                                    <td>{{ $ticket->subject }}</td>
                                    <td>{{ $ticket->name }}</td>
                                    <td>{{ $ticket->updated_at }}</td>
                                </tr>
                            @endforeach

                                <?php echo '<pre>'; ?>
                                <?php print_r($tickets); ?>
                                <?php echo '</pre>'; ?>
                        </tbody>
                    </table>

            @endif
        </div>
    </div>
</div>

<!-- Ticket modal window -->
<div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content grg-modal">
            <div class="modal-body">
                <div class="modalContent col-md-12">

                    <div class="pull-right">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <header style="margin-bottom: 60px;">
                        <h4 class="pull-left"><i class="fa fa-question-circle"></i>&nbsp;{{ trans('contacts.modal_label') }}</h4>
                    </header>
                    {!! Form::open(['url' => '/contacts/message', 'method' => 'POST']) !!}
                    <div class="form-group">
                        {!! Form::label('division', trans('contacts.division')) !!}
                        {!! Form::select('division', ['Technical Support', 'Sales', 'Administrators'], null, array('class' => 'form-control')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('subject', trans('contacts.subject')) !!}
                        {!! Form::input('subject','subject', null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('message', trans('contacts.message')) !!}
                        <textarea name="message" class="form-control"></textarea>
                    </div>
                    <br>
                    <div class="form-group">
                        {!! Form::submit(trans('contacts.submit'), ['class' => 'btn btn-pink']) !!}
                    </div>
                    {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
</div>

@stop

@section('scripts')

@stop