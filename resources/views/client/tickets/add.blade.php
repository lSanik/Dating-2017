 <!-- Ticket create modal window -->
    <div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content grg-modal">
                <div class="modal-body">
                    <div class="modalContent grg-modal-content col-md-12">

                        <div class="pull-right">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <header style="margin-bottom: 60px;">
                            <h4 class="pull-left"><i class="fa fa-question-circle"></i>&nbsp;&nbsp;{{ trans('contacts.modal_label') }}</h4>
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

                        {!! Form::open(['url' => '/contacts/message/', 'class' => 'form', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'files' => true]) !!}
                        <div class="form-group">
                            {!! Form::label('reason', trans('contacts.reason')) !!}
                            <select name="reason" class="form-control">
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->name }}">{{ trans('support.'.$subject->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            {!! Form::label('subject', trans('contacts.subject')) !!}
                            {!! Form::input('subject','subject', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('message', trans('contacts.message')) !!}
                            <textarea name="message" class="form-control" rows="5"></textarea>
                        </div>
                        <div class="form-group">
                            {!! Form::label('download_file', trans('contacts.download_file')) !!}<br/>
                            <input type="file" class="form-control file" name="avatar" accept="image/*, .doc, .txt, .pdf">
                        </div>
                        <br>
                        <div class="form-group">
                            {!! Form::submit(trans('contacts.submit'), ['class' => 'btn btn-pink grg-form-button']) !!}
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>