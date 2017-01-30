@extends('client.profile.profile')

@section('profileContent')

    <div class="col-md-8">
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

            @foreach($messages as $m)
                <div class="message">
                    <div class="photo"><img src="{{ url('uploads/'.$m->ava) }}" width="150px"></div>
                    <div class="name">{{ $m->name }}</div>
                    <div class="text-left">{{ $m->message }}</div>
                </div>
            @endforeach
        </div>
        <div class="send-form">
            {!! Form::open(['url' => '/'.App::getLocale().'/profile/'.$to.'/message' ]) !!}

                {!! Form::hidden('from', Auth::user()->id) !!}
                {!! Form::hidden('to', $to) !!}
                <div class="form-group">
                  {!! Form::textarea('message', null, ['class' => 'form-control', 'rows' => '3']) !!}
                </div>
                <div class="form-group text-right">
                    <input type="submit" class="btn btn-pink" value="{{ trans('mail.send') }}">
                </div>

            {!! Form::close() !!}
        </div>
    </div>

    <div class="col-md-4 container-right-list">
        <div id="user" class="bordered">
            Contact list
        </div>
        <ul class="tab">
            <li><a href="javascript:void(0)" class="tablinks active" onclick="openCity(event, 'tab-chat')"><i class="fa fa-comments-o" aria-hidden="true"></i>
                </a></li>
            <li><a href="javascript:void(0)" class="tablinks" onclick="openCity(event, 'tab-like')"><i class="fa fa-heart" aria-hidden="true"></i></a></li>
            <li><a href="javascript:void(0)" class="tablinks" onclick="openCity(event, 'tab-block')"><i class="fa fa-ban" aria-hidden="true"></i></a></li>
        </ul>
        <?php
        $tab_chat =  '<div id="tab-chat" class="tabcontent" style="display: block;">';
        $tab_like =  '<div id="tab-like" class="tabcontent">';
        $tab_block = '<div id="tab-block" class="tabcontent">';
        ?>
        @foreach($user_contact_list_data as $user_counter)
            @foreach($user_counter['user_data'] as $contact)
                @if($user_counter['contact_info']->status == 1)
                    <?php
                    $url=url('/uploads/'.$contact->avatar);
                    $data_bday= date('Y-m-d') - $contact->birthday;
                    $is_inline=($contact->sessionID==null)?('no'):('');
                    $tab_chat.="
                                        <div class=\"contact\">
                                            <a  href=\"#contact\" data-id=\"".$contact->sessionID."\">
                                                    <div class=\"online\">
                                                    <div class=\"is_online ".$is_inline."\">
                                                    </div>
                                                    </div>
                                                <div class=\"image\">
                                                    <img style=\"width: 100%;border-radius: 50px;\" src=\"".$url."\">
                                                </div>
                                                <div class=\"name\">
                                                    <span>". $contact->first_name . $contact->last_name ."</span>
                                                </div>
                                                <div class=\"age\">
                                                    <span>(".$data_bday.")</span>
                                                </div>
                                            </a>
                                        </div>
                                    ";?>
                @elseif($user_counter['contact_info']->status == 2)
                    <?php
                    $url=url('/uploads/'.$contact->avatar);
                    $data_bday= date('Y-m-d') - $contact->birthday;
                    $is_inline=($contact->sessionID==null)?('no'):('');
                    $tab_like.="
                                        <div class=\"contact\">
                                            <a  href=\"#contact\" data-id=\"".$contact->sessionID."\">
                                                    <div class=\"online\">
                                                    <div class=\"is_online ".$is_inline."\">
                                                    </div>
                                                    </div>
                                                <div class=\"image\">
                                                    <img style=\"width: 100%;border-radius: 50px;\" src=\"".$url."\">
                                                </div>
                                                <div class=\"name\">
                                                    <span>". $contact->first_name . $contact->last_name ."</span>
                                                </div>
                                                <div class=\"age\">
                                                    <span>(".$data_bday.")</span>
                                                </div>
                                            </a>
                                        </div>
                                    ";?>
                @elseif($user_counter['contact_info']->status == 3)
                    <?php
                    $url=url('/uploads/'.$contact->avatar);
                    $data_bday= date('Y-m-d') - $contact->birthday;
                    $is_inline=($contact->sessionID==null)?('no'):('');
                    $tab_block.="
                                        <div class=\"contact\">
                                            <a  href=\"#contact\" data-id=\"".$contact->sessionID."\">
                                                    <div class=\"online\">
                                                    <div class=\"is_online ".$is_inline."\">
                                                    </div>
                                                    </div>
                                                <div class=\"image\">
                                                    <img style=\"width: 100%;border-radius: 50px;\" src=\"".$url."\">
                                                </div>
                                                <div class=\"name\">
                                                    <span>". $contact->first_name . $contact->last_name ."</span>
                                                </div>
                                                <div class=\"age\">
                                                    <span>(".$data_bday.")</span>
                                                </div>
                                            </a>
                                        </div>
                                    ";?>
                @endif
            @endforeach
        @endforeach
        <?php
        $tab_chat.= '</div>';
        $tab_like.= '</div>';
        $tab_block.=  '</div>';
        ?>
        <div id="contacts" class="bordered">
            {!! $tab_chat !!}
            {!! $tab_like !!}
            {!! $tab_block !!}
        </div>
    </div>



    <script>
        function openCity(evt, cityName) {
            // Declare all variables
            var i, tabcontent, tablinks;

            // Get all elements with class="tabcontent" and hide them
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }

            // Get all elements with class="tablinks" and remove the class "active"
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }

            // Show the current tab, and add an "active" class to the link that opened the tab
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += " active";
        }
    </script>

@stop

@section('styles')
    <style>
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


        .send-form{

        }
        #contacts .contact{
            display: inline-block;
            width: 100%;
        }
        #contacts a{
            text-decoration: none;
        }
        #contacts .contact:hover{
            background-color: rgba(189, 189, 189, 0.5);
        }
        #contacts .contact .online{
            display: inline-block;
            width: 5%;
            margin: 0px -5px 0px 10px;
        }
        #contacts .contact .online .is_online.no{
            background-color: red;
            border: 1px solid red;
        }
        #contacts .contact .online .is_online{
            width: 10px;
            height: 10px;
            border: 1px solid green;
            border-radius: 50px;
            background-color: green;
        }
        #contacts .contact .image{
            display: inline-block;
            width: 25%;
            height: auto;
            padding: 10px;
        }
        #contacts .contact .name{
            display: inline-block;
            width: 50%;
        }
        #contacts .contact .age{
            display: inline-block;
            width: 10%;
        }
        /* Style tabs list */
        ul.tab {
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
            border: 1px solid #ccc;
            background-color: #f1f1f1;
        }

        /* Float the list items side by side */
        ul.tab li {float: left; width: 33.3%;}

        /* Style the links inside the list items */
        ul.tab li a {
            display: inline-block;
            color: black;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            transition: 0.3s;
            font-size: 17px;
            width: 100%;
        }

        /* Change background color of links on hover */
        ul.tab li a:hover {background-color: #ddd;}

        /* Create an active/current tablink class */
        ul.tab li a:focus, .active {background-color: #ccc;}

        /* Style the tab content */
        .tabcontent {
            display: none;
            padding: 6px 12px;
            border: 1px solid #ccc;
            border-top: none;
        }
    </style>
@stop