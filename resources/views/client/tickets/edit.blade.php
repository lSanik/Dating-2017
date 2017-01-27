<!-- Ticket edit modal window -->
<div class="modal fade" id="ticketEditModal-{{ $ticket->id }}" tabindex="-1" role="dialog" aria-labelledby="ticketEditModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content grg-modal">
            <div class="modal-body">
                <div class="modalContent grg-modal-content col-md-12">
                    <div class="pull-right">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <header style="margin-bottom: 60px;">
                        <h4 class="pull-left"><i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;{{ trans('contacts.subject') }}: {{ $ticket->subject }}</h4>
                    </header>

                    @if( Session::has('success_message') )
                        <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ Session::get('success_message') }}
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

                    <div class="messages">

                        <div class="message">
                            <div class="photo"><img src="{{ url('/uploads/'.$ticket->avatar) }}" width="150px"></div>
                            <div class="name"><strong>{{ $ticket->first_name }}</strong>  <i>{{ date('Y-m-d H:s', strtotime($ticket->updated_at)) }}</i></div>
                            <div class="text-left">{{ $ticket->message }}</div>

                        </div>
                        @foreach($this_r->getReply($ticket->id) as $reply)
                            @if($ticket->from != $reply->r_uid)
                                <div class="message-support">
                            @else
                                    <div class="message">
                            @endif
                                <div class="photo"><img src="{{ url('/uploads/'.$reply->avatar) }}" width="150px"></div>
                                        <div class="name"><strong>{{ $reply->first_name }}</strong>  <i>{{ date('Y-m-d H:s', strtotime($reply->updated_at)) }}</i></div>
                                <div class="text-left">{{ $reply->reply }}</div>
                            </div>
                        @endforeach

                    </div>
                    <br>
                    @if($ticket->name == 'closed')
                         <p><i class="fa fa-lock"></i><strong> {{ trans('contacts.ticket_closed') }}</strong></p>
                                @else
                        {!! Form::open(['url' => 'contacts/tickets/reply/'.$ticket->id, 'class' => 'form', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                        <div class="form-group">
                            {!! Form::label('reply', trans('contacts.message')) !!}
                            <textarea name="reply" class="form-control" rows="5"></textarea>
                        </div>
                        <div class="form-group">
                            {!! Form::submit(trans('contacts.send_message'), ['class' => 'btn btn-pink grg-form-button']) !!}
                            <input type="button" value="{{ trans('contacts.problem_solved') }}" class="btn btn-white grg-form-button"
                                   onclick='location.href="{{ url('contacts/tickets/close/'.$ticket->id) }}"'>
                        </div>
                        {!! Form::close() !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>