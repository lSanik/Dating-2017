@extends('client.app')

@section('content')

<style>
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
    <div class="container-fluid content-bg">
        <div class="row map-bg">
            <div class="col-md-10 col-md-offset-1" id="chat_container">
                <div class="col-md-12">
                    <div class="col-md-3 bordered" id="users_online">

                    </div>

                    <div class="col-md-6" id="center">
                        <div class="col-md-12 bordered" id="chat_messages_area">
                            <div id="chat_header">
                                <div class="pull-left">
                                    {{ trans('chat.chat') }}
                                </div>
                                <div class="pull-right">
                                    chat start / stop | ballance | time
                                </div>
                            </div>
                            <div id="messages">
                                <!--<div class="__item_from"><img width="32px"/>Message 1</div>
                                <div class="__item_to"><img width="32px"/>Message 2</div>-->
                                <div class="hello">Выберите контакт</div>
                            </div>
                        </div>
                        <div class="col-md-6 text-center bordered" id="chat_send_form">
                            {!! Form::open(['id' => 'chat_form']) !!}
                            <textarea class="form-control" name="message"></textarea>
                            {!! Form::submit(trans('chat.send'), ['class' => 'btn btn-pink']) !!}
                            {!! Form::close() !!}
                        </div>
                        <div class="col-md-6 bordered" id="chat_video">
                            VIDEO
                        </div>
                    </div>
                    <div class="col-md-3 container-right-list">
                        <div id="user" class="bordered">
                            PHOTO
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
                </div>
            </div>
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

    <div class="popup-send-invite">
        <div class="chat-invite">Пригласить в чат <span class="name">Наталья </span></div>
        <a id="chat_yes" style="cursor:pointer;">Да</a>
        <a id="chat_no" style="cursor:pointer;">Нет</a>
    </div>

    <div class="popup-get-invite">
        <div class="chat-invite">Приглашение в чат от<span class="name">Наталья </span></div>
        <div class="chat-invite">Принять?</div>
        <a id="chat_yes_get" style="cursor:pointer;">Да</a>
        <a id="chat_no_get" style="cursor:pointer;">Нет</a>
    </div>

    <script>
        window.onload = function() {

            jQuery("#contacts a").on("click", function () {
                //console.log(jQuery(this).data('id'));
                chat_partner=jQuery(this);
                jQuery('.popup-send-invite').css("display","block");
            });

            jQuery("#chat_yes").on("click", function () {
                jQuery('.popup-send-invite').css("display","none");
                //console.log(chat_partner.data('id'));
                createCookie('send_to_id',chat_partner.data('id'),'1');
                send_to(null,chat_partner.data('id'),'invite');
            });

            jQuery("#chat_no").on("click", function () {
                jQuery('.popup-send-invite').css("display","none");
            });

// window.chat.readyState
            chatConnect();
            function sec() {
                 if(window.chat.readyState!=1){
                 console.log('Connection lost... Retry');
                    chatConnect();
                 }
            }
            setInterval(sec, 5000);// использовать функцию

            function chatConnect(){
                createCookie("user_key",'{{session()->getId()}}','100');
                window.chat = new WebSocket('ws://{{$_SERVER['SERVER_NAME']}}:8080');

                window.chat.onopen = function (e) {
                    console.log("Connection established!");
                };
                //var recived_message=[];
                window.chat.onmessage = function (e) {
                    console.log('Получены данные: ' + e.data);
                    recived_message = e.data;
                    console.log(recived_message);
                    if(JSON.parse(recived_message).status=='send_invite'){
                        var invite = JSON.parse(recived_message);
                        jQuery('.popup-get-invite .name').html(invite.from_user_data['first_name']);
                        jQuery('.popup-get-invite').css("display","block");


                    }else if(JSON.parse(recived_message).status=='invite_accepted'){
                        window.chat_oponent_id=readCookie('send_to_id');
                        chat_started(JSON.parse(recived_message));
                    }
                    jQuery("#chat_yes_get").on("click", function () {
                        jQuery('.popup-get-invite').css("display","none");
                        console.log(invite);
                        chat_accept_invite(invite);
                    });
                    jQuery("#chat_no_get").on("click", function () {
                        jQuery('.popup-get-invite').css("display","none");
                    });
                    //console.log(JSON.parse(recived_message));
                    //on_message();
                };
            }
            function chat_accept_invite(invite){
                //console.log(invite);
                send_to(null,invite.from_user_data[0]['sessionID'],'accepted');
                window.chat_oponent_id=invite.from_user_data[0]['sessionID'];
                chat_started(invite);
            }
            function chat_started(data){
                console.log('chat started');

                jQuery('#messages').html("Чат начат");
                window.chat_messages=jQuery('#messages');
                window.init_chat=data;
                //console.log(data);
                //window.chat_oponent_id=data.from_user_data[0]['sessionID'];
                //initSend(jQuery('#chat_form'));
            }
            function chat_started_acepted(data){
                console.log('chat started acepted');
                console.log(data);
                jQuery('#messages').html("Чат начат");
               // window.chat_messages=jQuery('#messages');
                //window.init_chat=data;
                console.log(data);
                //window.chat_oponent_id=data.from_user_data[0]['sessionID'];
                //initSend(jQuery('#chat_form'));
            }

            function initSend(submit_event) {

                    submit_event.submit(function (e) {
                    e.preventDefault();
                    var chat_message=jQuery('#chat_form textarea').val();

                    if (window.init_chat !== undefined) {
                        console.log(window.chat_oponent_id);
                        console.log(chat_message);
                        send_to(chat_message,window.chat_oponent_id,'message');
                    } else{
                        alert('Выберите собеседника');
                    }
                    return false;
                });
            }
            initSend(jQuery('#chat_form'));
            //console.log(recived_message);
            function send_to(message, to_id, status) {
                var data = {
                    'to_id': to_id,
                    'message': message,
                    'value': status
                };
                console.log(data);
                window.chat.send(JSON.stringify(data));
                //console.log(data);
            };
        }
        function createCookie(name,value,days) {
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days*24*60*60*1000));
                var expires = "; expires=" + date.toUTCString();
            }
            else var expires = "";
            document.cookie = name + "=" + value + expires + "; path=/";
        }

        function readCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        }
    </script>
@stop
@section('styles')
    <style>
        .popup-get-invite .name{
            display: inline-block;
            width: 100%;
        }
        .popup-get-invite{
            box-shadow: 5px 5px 10px black;
            max-width: 200px;
            height: 85px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(255, 255, 255, 0.97)!important;
            overflow: hidden!important;
            -webkit-transition: .6s ease-out!important;
            -moz-transition: .6s ease-out!important;
            -o-transition: .6s ease-out!important;
            transition: .6s ease-out!important;
            margin: auto!important;
            z-index: 200001!important;
            text-align: center;
            display:none;
        }
        .popup-send-invite{
            box-shadow: 5px 5px 10px black;
            max-width: 200px;
            height: 55px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(255, 255, 255, 0.97)!important;
            overflow: hidden!important;
            -webkit-transition: .6s ease-out!important;
            -moz-transition: .6s ease-out!important;
            -o-transition: .6s ease-out!important;
            transition: .6s ease-out!important;
            margin: auto!important;
            z-index: 200001!important;
            text-align: center;
            display:none;
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

        .bordered{
            border: 1px solid #ccc;
        }

        #users_online{
            height: 55vh;
        }

        #chat_container{
            padding:30px;
        }

        #chat_messages_area{
            height: 55vh;
            background: white;

        }

        #chat_header{
            height: 5vh;
            border-bottom: 1px solid #ec2860;
        }

        #messages{
            position: absolute;
            overflow: auto;
            height: 50vh;
        }

        #messages > div {
            margin: 15px;
        }

    </style>
@stop
@section('script')

@stop