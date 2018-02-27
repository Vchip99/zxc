@extends('dashboard.dashboard')
@section('dashboard_header')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">

#frame {
  width: 100%;
  /* min-width: 360px;*/
  height: 80vh;
  min-height: 300px;
  max-height: 720px;
  background: #E6EAEA;
}
@media screen and (max-width: 325px) {
    .v-container .container {
    padding-left: 0px !important;
    padding-right: 0px !important;
  }
  /*.row .col-sm-9{
    padding-right: 0px !important;
  }
  .v-container .container .container {
    padding-right: 5px !important;
    padding-left: 5px !important;
  }
  #frame {
    max-width: 280px !important;
    height: 100vh;
  }
  #frame #sidepanel {
    width: 58px;
    min-width: 58px;
  }

  #frame .content {
    max-width: 206px !important;
  }
  #frame .content .contact-profile {
    max-width: 200px !important;
  }

  #frame .content .messages {
    width: 202px !important;
    padding: 0px !important;
  }

  #frame .content .message-input .wrap {
    position: relative;
    width: 250px !important;
  }*/
  ul#contact_list {
    margin-top: 0;
    margin-bottom: 10px;
    padding-left: 6px !important;
  }

  ul#chat_messages {
    margin-top: 0;
    margin-bottom: 10px;
    padding-left: 6px !important;
  }
  #frame .content .message-input .wrap input{
    width: 90% !important;
  }
  #frame .content .message-input .wrap button {
    padding: 16px 0;
    float: left;
    width: 10% !important;
  }
}

@media screen and (max-width: 380px) {
    .v-container .container {
    padding-left: 0px !important;
    padding-right: 0px !important;
  }
  ul#contact_list {
    margin-top: 0;
    margin-bottom: 10px;
    padding-left: 20px !important;
  }

  ul#chat_messages {
    margin-top: 0;
    margin-bottom: 10px;
    padding-left: 6px !important;
  }
  #frame .content .message-input .wrap input{
    width: 90% !important;
  }
  #frame .content .message-input .wrap button {
    padding: 16px 0;
    float: left;
    width: 10% !important;
  }
}

@media screen and (max-width: 735px) {
    .v-container .container {
    padding-left: 0px !important;
    padding-right: 0px !important;
  }
  ul#contact_list {
    margin-top: 0;
    margin-bottom: 10px;
    padding-left: 20px !important;
  }

  ul#chat_messages {
    margin-top: 0;
    margin-bottom: 10px;
    padding-left: 6px !important;
  }
  #frame .content .message-input .wrap input{
    width: 90% !important;
  }
  #frame .content .message-input .wrap button {
    padding: 16px 0;
    float: left;
    width: 10% !important;
  }
}

ul#chat_messages div{
  padding-right: 15px !important;
}

#frame #sidepanel {
  float: left;
 /* min-width: 280px;
  max-width: 340px;*/
  width: 30%;
  height: 100%;
  background: #2c3e50;
  color: #f5f5f5;
  overflow: hidden;
  position: relative;
}

#frame #sidepanel #profile {
  width: 80%;
  margin: 25px auto;
}

#frame #sidepanel #profile.expanded .wrap {
  height: 210px;
  line-height: initial;
}

#frame #sidepanel #profile.expanded .wrap p {
  margin-top: 20px;
}

#frame #sidepanel #profile.expanded .wrap i.expand-button {
  -moz-transform: scaleY(-1);
  -o-transform: scaleY(-1);
  -webkit-transform: scaleY(-1);
  transform: scaleY(-1);
  filter: FlipH;
  -ms-filter: "FlipH";
}

#frame #sidepanel #profile .wrap {
  height: 60px;
  line-height: 60px;
  overflow: hidden;
  -moz-transition: 0.3s height ease;
  -o-transition: 0.3s height ease;
  -webkit-transition: 0.3s height ease;
  transition: 0.3s height ease;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #profile .wrap {
    height: 55px;
  }
}

#frame #sidepanel #profile .wrap img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  padding: 3px;
  border: 2px solid #e74c3c;
  float: left;
  cursor: pointer;
  -moz-transition: 0.3s border ease;
  -o-transition: 0.3s border ease;
  -webkit-transition: 0.3s border ease;
  transition: 0.3s border ease;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #profile .wrap img {
    width: 40px;
    height: 40px;
    margin-left: 4px;
  }
}

#frame #sidepanel #profile .wrap img.online {
  border: 2px solid #2ecc71;
}
#frame #sidepanel #profile .wrap img.away {
  border: 2px solid #f1c40f;
}
#frame #sidepanel #profile .wrap img.busy {
  border: 2px solid #e74c3c;
}
#frame #sidepanel #profile .wrap img.offline {
  border: 2px solid #95a5a6;
}
#frame #sidepanel #profile .wrap p {
  float: left;
  margin-left: 15px;
}

@media screen and (max-width: 735px) {
  #frame #sidepanel #profile .wrap p {
    display: none;
  }
}
#frame #sidepanel #profile .wrap i.expand-button {
  float: right;
  margin-top: 23px;
  font-size: 0.8em;
  cursor: pointer;
  color: #435f7a;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #profile .wrap i.expand-button {
    display: none;
  }
}
#frame #sidepanel #profile .wrap #status-options {
  position: absolute;
  opacity: 0;
  visibility: hidden;
  width: 150px;
  margin: 70px 0 0 0;
  border-radius: 6px;
  z-index: 99;
  line-height: initial;
  background: #435f7a;
  -moz-transition: 0.3s all ease;
  -o-transition: 0.3s all ease;
  -webkit-transition: 0.3s all ease;
  transition: 0.3s all ease;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #profile .wrap #status-options {
    width: 58px;
    margin-top: 57px;
  }
}
#frame #sidepanel #profile .wrap #status-options.active {
  opacity: 1;
  visibility: visible;
  margin: 75px 0 0 0;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #profile .wrap #status-options.active {
    margin-top: 62px;
  }
}

#frame #sidepanel #profile .wrap #status-options:before {
  content: '';
  position: absolute;
  width: 0;
  height: 0;
  border-left: 6px solid transparent;
  border-right: 6px solid transparent;
  border-bottom: 8px solid #435f7a;
  margin: -8px 0 0 24px;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #profile .wrap #status-options:before {
    margin-left: 23px;
  }
}
#frame #sidepanel #profile .wrap #status-options ul {
  overflow: hidden;
  border-radius: 6px;
}
#frame #sidepanel #profile .wrap #status-options ul li {
  padding: 15px 0 30px 18px;
  display: block;
  cursor: pointer;
}
#contact_list li{
  list-style:none;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #profile .wrap #status-options ul li {
    padding: 15px 0 35px 22px;
  }
}
#frame #sidepanel #profile .wrap #status-options ul li:hover {
  background: #496886;
}
#frame #sidepanel #profile .wrap #status-options ul li span.status-circle {
  position: absolute;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  margin: 5px 0 0 0;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #profile .wrap #status-options ul li span.status-circle {
    width: 14px;
    height: 14px;
  }
}
#frame #sidepanel #profile .wrap #status-options ul li span.status-circle:before {
  content: '';
  position: absolute;
  width: 14px;
  height: 14px;
  margin: -3px 0 0 -3px;
  background: transparent;
  border-radius: 50%;
  z-index: 0;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #profile .wrap #status-options ul li span.status-circle:before {
    height: 18px;
    width: 18px;
  }
}
#frame #sidepanel #profile .wrap #status-options ul li p {
  padding-left: 12px;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #profile .wrap #status-options ul li p {
    display: none;
  }
}
#frame #sidepanel #profile .wrap #status-options ul li#status-online span.status-circle {
  background: #2ecc71;
}
#frame #sidepanel #profile .wrap #status-options ul li#status-online.active span.status-circle:before {
  border: 1px solid #2ecc71;
}
#frame #sidepanel #profile .wrap #status-options ul li#status-away span.status-circle {
  background: #f1c40f;
}
#frame #sidepanel #profile .wrap #status-options ul li#status-away.active span.status-circle:before {
  border: 1px solid #f1c40f;
}
#frame #sidepanel #profile .wrap #status-options ul li#status-busy span.status-circle {
  background: #e74c3c;
}
#frame #sidepanel #profile .wrap #status-options ul li#status-busy.active span.status-circle:before {
  border: 1px solid #e74c3c;
}
#frame #sidepanel #profile .wrap #status-options ul li#status-offline span.status-circle {
  background: #95a5a6;
}
#frame #sidepanel #profile .wrap #status-options ul li#status-offline.active span.status-circle:before {
  border: 1px solid #95a5a6;
}

#frame #sidepanel #profile .wrap #expanded {
  padding: 100px 0 0 0;
  display: block;
  line-height: initial !important;
}
#frame #sidepanel #profile .wrap #expanded label {
  float: left;
  clear: both;
  margin: 0 8px 5px 0;
  padding: 5px 0;
}
#frame #sidepanel #profile .wrap #expanded input {
  border: none;
  margin-bottom: 6px;
  background: #32465a;
  border-radius: 3px;
  color: #f5f5f5;
  padding: 7px;
  width: calc(100% - 43px);
}
#frame #sidepanel #profile .wrap #expanded input:focus {
  outline: none;
  background: #435f7a;
}
#frame #sidepanel #search,#frame .content #search_mobile {
  border-top: 1px solid #32465a;
  border-bottom: 1px solid #32465a;
  font-weight: 300;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #search {
    display: none;
  }
}
@media screen and (min-width: 735px) {
  .container #search_mobile {
    display: none;
  }
}
#frame #sidepanel #search label, .container #search_mobile label {
  position: absolute;
  margin: 10px 0 0 20px;
}
#frame #sidepanel #search input {
  font-family: "proxima-nova",  "Source Sans Pro", sans-serif;
  padding: 10px 0 10px 46px;
  width: calc(100% - 25px);
  border: none;
  background: #32465a;
  color: #f5f5f5;
}
/*.container #search_mobile label {
  font-family: "proxima-nova",  "Source Sans Pro", sans-serif;
  border: none;
  color: #000000;
  background: #f5f5f5;
}*/
#frame #sidepanel #search input:focus {
  outline: none;
  background: #435f7a;
}
#frame #sidepanel #search input::-webkit-input-placeholder {
  color: #f5f5f5;
}
#frame #sidepanel #search input::-moz-placeholder {
  color: #f5f5f5;
}
#frame #sidepanel #search input:-ms-input-placeholder {
  color: #f5f5f5;
}
#frame #sidepanel #search input:-moz-placeholder {
  color: #f5f5f5;
}

#frame #sidepanel #contacts {
  height: calc(100% - 168px);
  overflow-y: scroll;
  overflow-x: hidden;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #contacts {
    height: calc(100% - 149px);
    overflow-y: scroll;
    overflow-x: hidden;
  }
  #frame #sidepanel #contacts::-webkit-scrollbar {
    display: none;
  }
}
#frame #sidepanel #contacts.expanded {
  height: calc(100% - 334px);
}
#frame #sidepanel #contacts::-webkit-scrollbar {
  width: 8px;
  background: #2c3e50;
}
#frame #sidepanel #contacts::-webkit-scrollbar-thumb {
  background-color: #243140;
}
#frame #sidepanel #contacts ul li.contact {
  position: relative;
  padding: 10px 0 0px 0;
  font-size: 0.9em;
  cursor: pointer;
}
@media screen and (max-width: 735px) {
  /*#frame #sidepanel #contacts ul li.contact {
    padding: 6px 0 15px 0px;
  }*/
}
#frame #sidepanel #contacts ul li.contact:hover {
  background: #32465a;
}
#frame #sidepanel #contacts ul li.contact.active {
  background: #32465a;
  border-right: 5px solid #435f7a;
}
#frame #sidepanel #contacts ul li.contact.active span.contact-status {
  border: 2px solid #32465a !important;
}
#frame #sidepanel #contacts ul li.contact .wrap {
  width: 88%;
  margin: 0 auto;
  position: relative;
  padding: 10px 0 15px 0px;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #contacts ul li.contact .wrap {
    width: 100%;
    padding: 10px 0 15px 0px !important;
  }
}
#frame #sidepanel #contacts ul li.contact .wrap span {
  position: absolute;
  left: 0;
  margin: -2px 0 0 -2px;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  border: 2px solid #2c3e50;
  background: #95a5a6;
}
#frame #sidepanel #contacts ul li.contact .wrap span.online {
  background: #2ecc71;
}
#frame #sidepanel #contacts ul li.contact .wrap span.away {
  background: #f1c40f;
}
#frame #sidepanel #contacts ul li.contact .wrap span.busy {
  background: #e74c3c;
}
#frame #sidepanel #contacts ul li.contact .wrap img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  float: left;
  margin-right: 10px;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #contacts ul li.contact .wrap img {
    margin-right: 0px;
  }
}
#frame #sidepanel #contacts ul li.contact .wrap .meta {
  padding: 5px 0 0 0;
}
@media screen and (max-width: 735px) {
  /*#frame #sidepanel #contacts ul li.contact .wrap .meta {
    display: none;
  }*/
}
#frame #sidepanel #contacts ul li.contact .wrap .meta .name {
  font-weight: 600;
}
#frame #sidepanel #contacts ul li.contact .wrap .meta .preview {
  margin: 5px 0 0 0;
  padding: 0 0 1px;
  font-weight: 400;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  -moz-transition: 1s all ease;
  -o-transition: 1s all ease;
  -webkit-transition: 1s all ease;
  transition: 1s all ease;
}
#frame #sidepanel #contacts ul li.contact .wrap .meta .preview span {
  position: initial;
  border-radius: initial;
  background: none;
  border: none;
  padding: 0 2px 0 0;
  margin: 0 0 0 1px;
  opacity: .5;
}

#frame #sidepanel #bottom-bar {
  position: absolute;
  width: 100%;
  bottom: 0;
}
#frame #sidepanel #bottom-bar button {
  float: left;
  border: none;
  width: 50%;
  padding: 10px 0;
  background: #32465a;
  color: #f5f5f5;
  cursor: pointer;
  font-size: 0.85em;
  font-family: "proxima-nova",  "Source Sans Pro", sans-serif;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #bottom-bar button {
    float: none;
    width: 100%;
    padding: 15px 0;
  }
}
#frame #sidepanel #bottom-bar button:focus {
  outline: none;
}
#frame #sidepanel #bottom-bar button:nth-child(1) {
  border-right: 1px solid #2c3e50;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #bottom-bar button:nth-child(1) {
    border-right: none;
    border-bottom: 1px solid #2c3e50;
  }
}
#frame #sidepanel #bottom-bar button:hover {
  background: #435f7a;
}
#frame #sidepanel #bottom-bar button i {
  margin-right: 3px;
  font-size: 1em;
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #bottom-bar button i {
    font-size: 1.3em;
  }
}
@media screen and (max-width: 735px) {
  #frame #sidepanel #bottom-bar button span {
    display: none;
  }
}

#frame .content {
  float: right;
  width: 70%;
  height: 100%;
  overflow: hidden;
  position: relative;
  padding-left: 0px !important;
  padding-right: 0px !important;
}

#frame .content .contact-profile {
  width: 100%;
  height: 60px;
  line-height: 60px;
  background: #f5f5f5;
}
#frame .content .contact-profile img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  float: left;
  margin: 9px 12px 0 9px;
}

#frame .content .messages {
  min-height: 500px;
  overflow: auto;
  width: 100%;
  z-index: 0;
  bottom:-1px;
}
@media screen and (max-width: 735px) {
  #frame .content .messages {
    max-height: calc(100% - 82px);
    min-height: 0px !important;
  }
}

@media screen and (min-width: 736px) {
  #frame .content .messages {
    max-height: calc(100% - 82px);
    min-height: 0px !important;
  }
}

#frame .content .messages::-webkit-scrollbar {
  width: 8px;
  background: transparent;
}
#frame .content .messages::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.3);
}
#frame .content .messages ul li {
  display: inline-block;
  clear: both;
  float: left;
  margin: 15px 15px 5px 15px;
  width: calc(100% - 25px);
  font-size: 0.9em;
}
/*#frame .content .messages ul li:nth-last-child(1) {
  margin-bottom: 20px;
}*/
#frame .content .messages ul li.sent img {
  margin: 6px 8px 0 0;
}
#frame .content .messages ul li.sent p {
  background: #435f7a;
  color: #f5f5f5;
}
#frame .content .messages ul li.replies img {
  float: right;
  margin: 6px 6px 0 8px;
}
#frame .content .messages ul li.replies p {
  background: #f5f5f5;
  float: right;
}
#frame .content .messages ul li img {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  float: left;
}
#frame .content .messages ul li p {
  display: inline-block;
  padding: 10px 15px;
  border-radius: 20px;
  max-width: 205px;
  line-height: 130%;
}
@media screen and (min-width: 735px) {
  #frame .content .messages ul li p {
    max-width: 300px;
  }
}

#frame .content .message-input {
  position: absolute;
  bottom: 0;
  width: 100%;
  z-index: 99;
}
#frame .content .message-input .wrap {
  position: relative;
}
#frame .content .message-input .wrap input {
  font-family: "proxima-nova",  "Source Sans Pro", sans-serif;
  float: left;
  border: none;
  width: calc(100% - 90px);
  padding: 11px 32px 10px 8px;
  font-size: 0.8em;
  color: #32465a;
}

#frame .content .message-input .wrap input:focus {
  outline: none;
}
#frame .content .message-input .wrap button {
  float: right;
  border: none;
  width: 80px;
  height: 36px;
  padding: 12px 0;
  cursor: pointer;
  background: #32465a;
  color: #f5f5f5;
}

#frame .content .message-input .wrap button:hover {
  background: #435f7a;
}
#frame .content .message-input .wrap button:focus {
  outline: none;
}
  </style>
@stop
@section('module_title')
  <section class="content-header">
    <h1> Chat Messages</h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-star"></i> Notifications</li>
      <li class="active">Chat Messages </li>
    </ol>
  </section>
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
@stop
@section('dashboard_content')
  <div class="container ">
    <div id="search_mobile" class="row">
      <!-- <label for=""><i class="fa fa-search" aria-hidden="true"></i></label> -->
      <input type="text" id="search_contact" name="student" class="form-control" placeholder="Search contacts..." onkeyup="searchContact(this.value);">
    </div>
    <div id="frame">
      <div id="sidepanel">
        <div id="profile">
          <div class="wrap">
            @if(is_file(Auth::user()->photo) || (!empty(Auth::user()->photo) && false == preg_match('/userStorage/',Auth::user()->photo)))
              <img src="{{ asset(Auth::user()->photo)}} " class="online" alt="User Image">
            @else
              <img src="{{ url('images/user/user1.png')}}" class="online" alt="User Image">
            @endif
            <p>{{Auth::user()->name}}</p>
          </div>
        </div>
        <div id="search">
          <label for=""><i class="fa fa-search" aria-hidden="true"></i></label>
          <input type="text" id="search_contact" name="student" class="form-control" placeholder="Search contacts..." onkeyup="searchContact(this.value);">
        </div>
        <div id="contacts">
          <ul id="contact_list">
            @foreach($users['users'] as $chatuser)
            <li class="contact" id="{{$chatuser['id']}}" data-user_name="{{$chatuser['name']}}" onclick="showChat(this);">
              <div class="wrap">
                @if(isset($onlineUsers[$chatuser['id']]))
                  <span id="status_{{$chatuser['id']}}" data-user_id="{{$chatuser['id']}}" class="contact-status online"></span>
                @else
                  <span id="status_{{$chatuser['id']}}" data-user_id="{{$chatuser['id']}}" class="contact-status"></span>
                @endif
                @if(is_file($chatuser['photo']) || (!empty($chatuser['photo']) && false == preg_match('/userStorage/',$chatuser['photo'])))
                  <img id="image_{{$chatuser['id']}}" src="{{ asset($chatuser['photo'])}}" class="online" alt="User Image">
                @else
                  <img id="image_{{$chatuser['id']}}" src="{{ url('images/user/user1.png')}}" class="online" alt="User Image">
                @endif
                <div class="meta">
                  <p class="name">{{$chatuser['name']}}
                    @if(isset($unreadCount[$chatuser['id']]))
                      <strong style="color: red;" id="unreadCount_{{$chatuser['id']}}">{{$unreadCount[$chatuser['id']]}}</strong>
                    @else
                      <strong style="color: red;" id="unreadCount_{{$chatuser['id']}}"></strong>
                    @endif
                  </p>
                  <p class="preview">{{$chatuser['college']}}</p>
                </div>
                <input type="hidden" id="message_limit_{{$chatuser['id']}}" value="0">
                <input type="hidden" id="is_scroll_{{$chatuser['id']}}" value="0">
              </div>
            </li>
            @endforeach
          </ul>
          <input type="hidden" id="isUserScroll" value="0">
          <input type="hidden" id="previuos_chat_users" value="{{$users['chat_users']}}">
        </div>
      </div>
      <div class="content">
        <div class="contact-profile">
          <img class="img-circle" id="profile_image" src="" alt="" />
          <p id="profile_name"></p>
        </div>
        <div class="messages chat_messages">
          <ul id="chat_messages">
          </ul>

        </div>
        <div class="message-input">
          <div class="wrap">
          <input type="text" id="sendTextMessage" data-receiver_id="" placeholder="Write your message..." onfocus="readmessagecount(this);"/>
          <button id="sendButton" data-send_id="" data-chatroom_id=""  onclick="sendMessage(this);"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript">
  var socket = io.connect(window.location.protocol+'//'+window.location.host+':8080', { secure: true, reconnect: true, rejectUnauthorized : false });

  $(document).ready(function () {
    $('#contact_list li:first')[0].click()
    $('#sendTextMessage').focus();
    $('.chat_messages').scroll(function() {
      if($(this).scrollTop() == 0){
        getNextchatMessages($(this).attr('id'));
      }
    });
    setInterval(checkOnlineUsers, 180000);
  });

  function searchContact(contact){
    if(contact.length > 2){
      $.ajax({
        method: "POST",
        url: "{{url('searchContact')}}",
        data:{contact:contact}
      })
      .done(function( results ) {
        if(results['users'].length > 0){
          renderContacts(results['users'],results['unreadCount'],results['onlineUsers']);
        } else {
          document.getElementById('contact_list').innerHTML = 'No Result';
        }
      });
    } else if('' == contact){
      $.ajax({
        method: "POST",
        url: "{{url('getContacts')}}",
      })
      .done(function( allresults ) {
        if(allresults['chatusers']['users'].length > 0){
          renderContacts(allresults['chatusers']['users'],allresults['chatusers']['unreadCount'],allresults['onlineUsers']);
        } else {
          document.getElementById('contact_list').innerHTML = 'No Result';
        }
      });
    }
  }

  function renderContacts(contacts,unreadCount,onlineUsers){
    var contactList = document.getElementById('contact_list');
    contactList.innerHTML = '';
    $.each(contacts, function(idx, obj){
        var liEle = document.createElement('li');
        liEle.className = 'contact';
        liEle.id = obj['id'];
        liEle.setAttribute('data-user_name', obj['name']);
        liEle.setAttribute('onclick', 'showChat(this);');

        var divEle = document.createElement('div');
        divEle.className = 'wrap';

        var spanStatus = document.createElement('span');
        spanStatus.id = 'status_'+obj['id'];
        spanStatus.setAttribute('data-user_id', obj['id']);
        if(onlineUsers && onlineUsers[obj['id']]){
          spanStatus.className = 'contact-status online';
        } else {
          spanStatus.className = 'contact-status';
        }
        divEle.appendChild(spanStatus);

        var imgUser = document.createElement('img');
        imgUser.id = 'image_'+obj['id'];
        var webUrl = window.location.protocol+'//'+window.location.host;
        if('other' == obj['image_exist']){
          imgUser.setAttribute('src', obj['photo']);
        } else if('system' == obj['image_exist']){
          imgUser.setAttribute('src', webUrl+"/"+obj['photo']);
        } else {
          imgUser.setAttribute('src', webUrl+'/images/user/user1.png');
        }
        imgUser.setAttribute('style', 'width: 40px;height: 40px;border-radius: 50%;float: left;margin-right: 10px;');

        imgUser.className = 'online';
        imgUser.setAttribute('alt', 'User Image');
        divEle.appendChild(imgUser);

        var divMeta = document.createElement('div');
        divMeta.innerHTML = '<p class="name">'+obj['name'];
        if( unreadCount && unreadCount[obj['id']] > 0){
          divMeta.innerHTML +='<strong style="color: red;" id="unreadCount_'+obj['id']+'">'+unreadCount[obj['id']]+'</strong>';
        } else {
          divMeta.innerHTML +='<strong style="color: red;" id="unreadCount_'+obj['id']+'"></strong>';
        }
        divMeta.innerHTML +='<p><p class="preview">'+obj['college']+'</p>';
        divEle.appendChild(divMeta);

        var inputMsgLimit = document.createElement('input');
        inputMsgLimit.id = 'message_limit_'+obj['id'];
        inputMsgLimit.setAttribute('type', 'hidden');
        inputMsgLimit.setAttribute('value', 0);
        divEle.appendChild(inputMsgLimit);

        var inputIsScroll = document.createElement('input');
        inputIsScroll.id = 'is_scroll_'+obj['id'];
        inputIsScroll.setAttribute('type', 'hidden');
        inputIsScroll.setAttribute('value', 0);
        divEle.appendChild(inputIsScroll);
        liEle.appendChild(divEle);
        contactList.appendChild(liEle);
    });
  }

  function checkOnlineUsers(){
    var token = "{{ csrf_token() }}";
    var current_user = document.getElementById('user_id').value;
    $.ajax({
        type: "POST",
        url: '{!! URL::to("checkDashboardOnlineUsers") !!}',
        dataType: "json",
        data: {'_token':token},
        success:function(onlineusers){
            if(onlineusers.length > 0){
                var messageUsers = $('span[id^=status_]');
                $.each(messageUsers, function(idx, obj){
                  var userId = $(obj).data('user_id');
                  if(current_user != userId){
                      if(onlineusers.indexOf(userId) > -1){
                          document.getElementById('status_'+userId).classList.add('online');
                      } else {
                          document.getElementById('status_'+userId).classList.remove('online');
                      }
                  }
                });
            }
        }
    });
  }

  // make count zero
  function readmessagecount(ele){
    var receiver = $(ele).attr('data-receiver_id');
    var current_user = document.getElementById('user_id').value;
    var token = "{{ csrf_token() }}";
    if(receiver > 0 && document.getElementById('unreadCount_'+receiver) && parseInt(document.getElementById('unreadCount_'+receiver).innerHTML) > 0){
      if(parseInt(document.getElementById('unreadCountDash_1_'+current_user).innerHTML) > 0){
        document.getElementById('unreadCountDash_1_'+current_user).innerHTML -= parseInt(document.getElementById('unreadCount_'+receiver).innerHTML);
        document.getElementById('unreadCountDash_2_'+current_user).innerHTML -= parseInt(document.getElementById('unreadCount_'+receiver).innerHTML);
      }
      $.ajax({
        type: "POST",
        url: '{!! URL::to("readChatMessages") !!}',
        dataType: "json",
        data: {'_token':token, 'sender_id':receiver},
        success:function(msg){}
      });
      document.getElementById('unreadCount_'+receiver).innerHTML = '';
    }
  }

  // show next 10 messages
  function getNextchatMessages(receiverId){
      var current_user = document.getElementById('user_id').value;
      // document.getElementById('sendButton').setAttribute('data-send_id',receiverId);
      var roomArr = [];
      roomArr.push(receiverId);
      roomArr.push(current_user);
      roomArr.sort();
      if(1 == document.getElementById('is_scroll_'+receiverId).value){
        // add chat in to popup
        var token = "{{ csrf_token() }}";
        var messageLimit = parseInt($('#message_limit_'+receiverId).val())+10;
        $.ajax({
            type: "POST",
            url: '{!! URL::to("dashboardPrivateChat") !!}',
            dataType: "json",
            data: {'_token':token,'receiver_id':receiverId, 'message_limit':messageLimit},
            success:function(messages){
                $('#message_limit_'+receiverId).val(messageLimit);
                var chatMessageId = document.getElementById('chatmessages_'+roomArr[0]+'_'+roomArr[1]);
                var ulChatWindows = document.getElementById('chat_messages');
                if(messages['messages'].length > 0){
                  var senderImgPath = $('#dashboardUserImage').attr('src');
                  var receiverImgPath = $($('li#'+receiverId+' .wrap img')[0]).attr('src');
                    $.each(messages['messages'],function(idx,obj){
                        if(current_user == obj.sender_id){
                            $(ulChatWindows).prepend('<li class="replies" id="'+obj.id+'"><img src="'+senderImgPath+'"><p>'+obj.message+'</p></li><span class="pull-right">'+messagteTime(obj.created_at)+'</span>');
                        } else {
                            $(ulChatWindows).prepend('<li class="sent" id="'+obj.id+'"><img src="'+receiverImgPath+'"><p>'+obj.message+'</p></li><span class="pull-left">'+messagteTime(obj.created_at)+'</span>');
                        }
                    });
                    $(ulChatWindows).parent().animate({scrollTop:10});
                    if(messages['chatroom_id']){
                      document.getElementById('sendButton').setAttribute('data-chatroom_id',messages['chatroom_id']);
                    }
                    document.getElementById('is_scroll_'+receiverId).value = 1;
                } else {
                    document.getElementById('is_scroll_'+receiverId).value = 0;
                }
            }
        });
      }
  }

  function showChat(ele){
    var receiverId = $(ele).attr('id');
    $('#chat_messages').parent().attr('id', receiverId);
    var receiverName = $(ele).data('user_name');
    var current_user = document.getElementById('user_id').value;
    $('#profile_image').attr('src',$('#image_'+receiverId).attr('src'));
    document.getElementById('profile_name').innerHTML = receiverName;
    var roomArr = [];
    roomArr.push(receiverId);
    roomArr.push(current_user);
    var roomMembers = roomArr.sort();
    var roomName = 'private_'+roomArr[0]+'_'+roomArr[1];
    socket.emit('subscribe', roomName);
    // add chat window
    ulChatWindows = document.getElementById('chat_messages');
    ulChatWindows.innerHTML = '';
    // add chat in to popup
    var token = "{{ csrf_token() }}";
    var messageLimit = 0;
    $('#message_limit_'+receiverId).val(0);
    document.getElementById('is_scroll_'+receiverId).value = 0;
    document.getElementById('sendButton').setAttribute('data-send_id',receiverId);
    document.getElementById('sendTextMessage').setAttribute('data-receiver_id',receiverId);
    document.getElementById('sendTextMessage').innerHTML = '';
    $.ajax({
        type: "POST",
        url: '{!! URL::to("dashboardPrivateChat") !!}',
        dataType: "json",
        data: {'_token':token,'receiver_id':receiverId, 'message_limit':messageLimit},
        success:function(messages){
          $('#message_limit_'+receiverId).val(10);
          var senderImgPath = $('#dashboardUserImage').attr('src');
          var receiverImgPath = $($('li#'+receiverId+' .wrap img')[0]).attr('src');
          $.each(messages['messages'],function(idx,obj){
            var elePTime = document.createElement('div');
            elePTime.innerHTML = messagteTime(obj.created_at);
            if(document.getElementById('user_id').value == obj.sender_id){
                elePTime.className = 'pull-right';
            } else {
                elePTime.className = 'pull-left';
            }
            ulChatWindows.prepend(elePTime);

            var liEle = document.createElement('li');
            if(document.getElementById('user_id').value == obj.sender_id){
                liEle.className = 'replies';
            } else {
                liEle.className = 'sent';
            }
            liEle.id = obj['id'];

            var spanImage = document.createElement('img');
            if(document.getElementById('user_id').value == obj.sender_id){
              spanImage.setAttribute('src',senderImgPath);
            } else {
              spanImage.setAttribute('src',receiverImgPath);
            }
            liEle.appendChild(spanImage);

            var eleP = document.createElement('p');
            eleP.innerHTML = obj.message;
            liEle.appendChild(eleP);

            ulChatWindows.prepend(liEle);
          });
            $(ulChatWindows).parent().animate({scrollTop:1000});
            document.getElementById('is_scroll_'+receiverId).value = 1;
            if(messages['chatroom_id']){
                document.getElementById('sendButton').setAttribute('data-chatroom_id',messages['chatroom_id']);
            }
        }
    });
  }

  function messagteTime(date){
      var currentTime = new Date(date),
      day = currentTime.getDate(),
      month = currentTime.getMonth() + 1,
      year = currentTime.getFullYear();

      hours = currentTime.getHours(),
      minutes = currentTime.getMinutes();

      if (minutes < 10) {
          minutes = "0" + minutes;
      }

      var suffix = "AM";
      if (hours >= 12) {
          suffix = "PM";
          hours = hours - 12;
      }
      if (hours == 0) {
          hours = 12;
      }
      return day + "/" + month + "/" + year+ ' ' + hours + ":" + minutes + " " + suffix;
  }

  // send & insert message
  function sendMessage(ele) {
      var id = $(ele).data('send_id');
      message = document.getElementById('sendTextMessage').value;
      if(message != '' && id != ''){
          var roomArr = [];
          roomArr.push(id);
          roomArr.push(document.getElementById('user_id').value);
          var roomMembers = roomArr.sort();
          var room = 'private_'+roomArr[0]+'_'+roomArr[1];
          // var user = document.getElementById('currentUserName').value;
          var chatroomId = $(ele).data('chatroom_id');
          var receiver = id;
          var sender = document.getElementById('user_id').value;
          var token = "{{ csrf_token() }}";
          var created_at = new Date();
          socket.emit('send', { room: room, message: message, sender:parseInt(sender) ,receiver:receiver, created_at:created_at});
          $.ajax({
              type: "POST",
              url: '{!! URL::to("dashboardSendMessage") !!}',
              dataType: "json",
              data: {'_token':token,'message':message,'sender':parseInt(sender) ,'receiver':receiver, 'chatroomId':chatroomId},
              success:function(data){
                  document.getElementById('sendTextMessage').value = '';
              }
          });
      }else{
        if(message == ''){
          alert("Please Add Message.");
        } else {
          alert("Please Select Chat User.");
        }
      }
  }

  window.onload = function () {
    // receive message
    socket.on('message', function (data) {
        var roomArr = [];
        var current_user = document.getElementById('user_id').value;
        roomArr.push(data.receiver);
        roomArr.push(data.sender);
        var roomMembers = roomArr.sort();
        var roomName = 'chatmessages_'+roomArr[0]+'_'+roomArr[1];
        var userchat = $('#chat_messages');

        if(document.getElementById('unreadCount_'+data.sender)){
          if(document.getElementById('unreadCount_'+data.sender).innerHTML > 0){
            document.getElementById('unreadCount_'+data.sender).innerHTML = parseInt(document.getElementById('unreadCount_'+data.sender).innerHTML) + 1;
          } else {
            document.getElementById('unreadCount_'+data.sender).innerHTML = 1;
          }
        }
        var senderImgPath = $('#dashboardUserImage').attr('src');
        var receiverImgPath = $($('li#'+data.sender+' .wrap img')[0]).attr('src');
        if(current_user ==  data.sender){
            var liEle = document.createElement('li');
            liEle.className = 'replies';
            liEle.innerHTML = '<img src="'+senderImgPath+'"><p>'+data.message+'</p>';
            userchat[0].appendChild(liEle);
            var spanEle = document.createElement('div');
            spanEle.className = 'pull-right';
            spanEle.innerHTML = messagteTime(data.created_at);
            userchat[0].appendChild(spanEle);
        } else {
            var liEle = document.createElement('li');
            liEle.className = 'sent';
            liEle.innerHTML = '<img src="'+receiverImgPath+'"><p>'+data.message+'</p>';
            userchat[0].appendChild(liEle);
            var spanEle = document.createElement('div');
            spanEle.className = 'pull-left';
            spanEle.innerHTML = messagteTime(data.created_at);
            userchat[0].appendChild(spanEle);
            document.getElementById('unreadCountDash_1_'+data.receiver).innerHTML = parseInt(document.getElementById('unreadCountDash_1_'+data.receiver).innerHTML) + 1;
            document.getElementById('unreadCountDash_2_'+data.receiver).innerHTML = parseInt(document.getElementById('unreadCountDash_2_'+data.receiver).innerHTML) + 1;
        }
        $('div.chat_messages').scrollTop($('div.chat_messages')[0].scrollHeight);
        $('#sendTextMessage').focus();
    });
  };

  $('#contacts').scroll(function() {
      if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
         if(0 == $('#isUserScroll').val()){
             var limitStart = $("#contact_list li").length;
             $('#isUserScroll').val(1);
             loadChatUsers(limitStart);
         }
      }
  });

  // show chat users
  function loadChatUsers(limitStart){
      var previuosChatUsers = document.getElementById('previuos_chat_users').value;
      var token = "{{ csrf_token() }}";
      $.ajax({
          type: "POST",
          url: '{!! URL::to("loadChatUsers") !!}',
          dataType: "json",
          data: {'_token':token, 'limit_start':limitStart, 'previuos_chat_users':previuosChatUsers},
          success:function(users){
              if(users['chatusers'].length > 0){
                  var chatUsers = document.getElementById('contact_list');
                  $.each(users['chatusers'],function(idx,obj){
                      var liEle = document.createElement('li');
                      if(document.getElementById('user_id').value == obj['id']){
                          liEle.className = 'hide contact';
                      } else {
                          liEle.className = 'contact';
                      }
                      liEle.id = obj['id'];
                      liEle.setAttribute('data-user_name', obj['name']);
                      liEle.setAttribute('onclick', 'showChat(this);');

                      var divWrap = document.createElement('div');
                      divWrap.className = 'wrap';

                      var spanStatus = document.createElement('span');
                      spanStatus.id = 'status_'+obj['id'];
                      spanStatus.setAttribute('data-user_id', obj['id']);
                      if(obj['is_online']){
                        spanStatus.className = 'contact-status online';
                      } else {
                        spanStatus.className = 'contact-status';
                      }
                      divWrap.appendChild(spanStatus);

                      var eleImage = document.createElement('img');
                      eleImage.id = 'image_'+obj['id'];
                      eleImage.className = 'online';
                      var spanImage = document.createElement('img');
                      if(obj['photo']){
                        var userImagePath = obj['photo'];
                      } else {
                        var userImagePath = "/images/user1.png";
                      }
                      eleImage.setAttribute('src', userImagePath);
                      divWrap.appendChild(eleImage);

                      var divmeta = document.createElement('div');
                      divmeta.className = 'meta';
                      divmeta.innerHTML = '<p class="name">'+obj['name']+'</p>';
                      if(users['unreadCount'] && users['unreadCount'][obj['id']] > 0){
                        divmeta.innerHTML +='<strong style="color: red;" id="unreadCount_'+obj['id']+'">'+users['unreadCount'][obj['id']]+'</strong>';
                      }
                      divmeta.innerHTML +='<p class="preview">'+obj['college']+'</p><input type="hidden" id="message_limit_'+obj['id']+'" value="0"><input type="hidden" id="is_scroll_'+obj['id']+'" value="0">';
                      divWrap.appendChild(divmeta);
                      liEle.appendChild(divWrap);
                      chatUsers.appendChild(liEle);
                  });
                  $('#isUserScroll').val(0);
              } else {
                  $('#isUserScroll').val(1);
              }
          }
      });
  }

</script>
@stop