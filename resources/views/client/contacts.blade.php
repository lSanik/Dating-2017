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
        .grg-ticket-closed{
            background-color: #DDDDDD;
        }

/* Ticket Edit */
        .messages {
            margin-top: 30px;
            height: 25vh;
            overflow-y: scroll;
            padding: 15px;
            border: 1px solid #ccc;
            background: #ffffff;
        }

        .message{
            width: 100%;
            word-break: break-all;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .message:nth-child(even){
            border: 1px solid #ccc;
            background-color: #ebebeb;
            clear: both;
            text-align: right;
        }
        .message:nth-child(even) .photo{
            float: right;
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
                                <tr id="edit-ticket" data-toggle="modal" data-target="#ticketEditModal"
                                    @if($ticket->name == 'closed')
                                        class="grg-ticket-closed"
                                    @endif
                                >
                                    <td>{{ $ticket->id }}</td>
                                    <td>{{ $ticket->subject }}</td>
                                    <td>{{ trans('contacts.'.$ticket->name) }}</td>
                                    <td>{{ date('Y-m-d H:s', strtotime($ticket->updated_at)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                {!! $tickets->render() !!}
            @endif
        </div>
    </div>
</div>

<!-- Ticket create modal window -->
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
                    {!! Form::open(['url' => '/contacts/message/', 'class' => 'form', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'files' => true]) !!}
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
                    <div class="form-group">
                        {!! Form::label('download_file', trans('contacts.download_file')) !!}<br/>
                        <input type="file" class="form-control file" name="avatar" accept="image/*, .doc, .txt, .pdf">
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

<!-- Ticket edit modal window -->
<div class="modal fade" id="ticketEditModal" tabindex="-1" role="dialog" aria-labelledby="ticketEditModalLabel">
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

                    <div class="messages">


                            <div class="message">
                                <div class="photo"><img src="#" width="150px"></div>
                                <div class="name">Катя</div>
                                <div class="text-left">
                                    Многие думают, что Lorem Ipsum - взятый с потолка псевдо-латинский набор слов,
                                    но это не совсем так. Его корни уходят в один фрагмент классической латыни
                                    45 года н.э., то есть более двух тысячелетий назад.
                                    Ричард МакКлинток, профессор латыни из колледжа Hampden-Sydney,
                                    штат Вирджиния, взял одно из самых странных слов в Lorem Ipsum,
                                    "consectetur", и занялся его поисками в классической латинской литературе.
                                </div>
                            </div>
                        <div class="message">
                            <div class="photo"><img src="#" width="150px"></div>
                            <div class="name">Катя</div>
                            <div class="text-left">
                                Многие думают, что Lorem Ipsum - взятый с потолка псевдо-латинский набор слов,
                                но это не совсем так. Его корни уходят в один фрагмент классической латыни
                                45 года н.э., то есть более двух тысячелетий назад.
                                Ричард МакКлинток, профессор латыни из колледжа Hampden-Sydney,
                                штат Вирджиния, взял одно из самых странных слов в Lorem Ipsum,
                                "consectetur", и занялся его поисками в классической латинской литературе.
                                В результате он нашёл неоспоримый первоисточник Lorem Ipsum в разделах
                                1.10.32 и 1.10.33 книги "de Finibus Bonorum et Malorum"
                                ("О пределах добра и зла"), написанной Цицероном в 45 году н.э.
                                Этот трактат по теории этики был очень популярен в эпоху Возрождения.
                            </div>
                        </div>
                        <div class="message">
                            <div class="photo"><img src="#" width="150px"></div>
                            <div class="name">Катя</div>
                            <div class="text-left">Меня зовут Катя</div>
                        </div>

                    </div>
                    <br>

                    {!! Form::open(['url' => '/contacts/message/', 'class' => 'form', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
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