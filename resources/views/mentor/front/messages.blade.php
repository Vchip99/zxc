@extends('mentor.front.master')
@section('title')
  <title>MENTOR - HOME</title>
@stop
@section('header-css')
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
    @media screen and (min-width: 735px) {
      div#admin{padding-left: 56px;}
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
      div#admin{padding-left: 18px;}
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
    #contact_list li,#admin li{
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
    #frame #sidepanel #contacts ul li.contact .wrap span{
      position: absolute;
      left: 0;
      margin: -2px 0 0 -2px;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      border: 2px solid #2c3e50;
      background: #95a5a6;
    }
    #admin li.contact .wrap span {
      position: absolute;
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
    #admin li.contact .wrap img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      float: left;
      margin-right: 10px;
    }

    #admin li.contact .wrap span.online {
        background: #2ecc71;
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
@section('content')
  @include('mentor.front.header_menu')
  <div class="container" style="margin-top: 90px;">
    <div id="frame">
      <div id="sidepanel">
        <div id="profile">
          <div class="wrap">
            @if(is_file(Auth::user()->photo) || (!empty(Auth::user()->photo) && false == preg_match('/userStorage/',Auth::user()->photo)))
              <img id="dashboardUserImage" src="{{ asset(Auth::user()->photo)}} " class="online" alt="User Image">
            @else
              <img id="dashboardUserImage" src="{{ url('images/user/user1.png')}}" class="online" alt="User Image">
            @endif
            <p>{{Auth::user()->name}}</p>
          </div>
        </div>
        <div id="contacts">
          <ul id="contact_list">
            @if(count($mentors) > 0)
            @foreach($mentors as $mentor)
            <li class="contact" id="{{$mentor['id']}}" data-user_name="{{$mentor['name']}}" onclick="showChat(this);">
              <div class="wrap">
                @if(is_file($mentor['photo']) || (!empty($mentor['photo']) && false == preg_match('/userStorage/',$mentor['photo'])))
                  <img id="image_{{$mentor['id']}}" src="{{ asset($mentor['photo'])}}" class="online" alt="User Image">
                @else
                  <img id="image_{{$mentor['id']}}" src="{{ url('images/user/user1.png')}}" class="online" alt="User Image">
                @endif
                <div class="meta">
                  <p class="name">{{$mentor['name']}}
                      <strong style="color: red;" id="unreadCount_{{$mentor['id']}}"></strong>
                  </p>
                </div>
                <input type="hidden" id="message_limit_{{$mentor['id']}}" value="0">
                <input type="hidden" id="is_scroll_{{$mentor['id']}}" value="0">
              </div>
            </li>
            @endforeach
            @endif
          </ul>
          <input type="hidden" id="isUserScroll" value="0">
          <input type="hidden" id="previuos_chat_users" value="">
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
          <input type="text" id="sendTextMessage" data-receiver_id="" placeholder="Write your message..."/>
          <button id="sendButton" data-send_id="" data-chatroom_id=""  onclick="sendMessage(this);"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop
@section('footer')
  @include('mentor.front.footer')
<script type="text/javascript">
  $(document).ready(function () {
    if( undefined != $('#contact_list li:first')[0]){
      $('#contact_list li:first')[0].click();
    }
  });
  function showChat(ele){
    var receiverId = $(ele).attr('id');
    $('#chat_messages').parent().attr('id', receiverId);
    var receiverName = $(ele).data('user_name');
    var current_user = document.getElementById('user_id').value;
    $('#profile_image').attr('src',$('#image_'+receiverId).attr('src'));
    document.getElementById('profile_name').innerHTML = receiverName;
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
        url: '{!! URL::to("userMentorPrivateChat") !!}',
        dataType: "json",
        data: {'_token':token,'sender_id':current_user,'receiver_id':receiverId, 'message_limit':messageLimit},
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
          // socket.emit('send', { room: room, message: message, sender:parseInt(sender) ,receiver:receiver, created_at:created_at});
          $.ajax({
              type: "POST",
              url: '{!! URL::to("userMentorSendMessage") !!}',
              dataType: "json",
              data: {'_token':token,'message':message,'sender':parseInt(sender) ,'receiver':receiver, 'chatroomId':chatroomId,'generated_by_mentor':0},
              success:function(messages){
                  document.getElementById('sendTextMessage').value = '';
                  ulChatWindows = document.getElementById('chat_messages');
                  ulChatWindows.innerHTML = '';
                  var senderImgPath = $('#dashboardUserImage').attr('src');
                  var receiverImgPath = $($('li#'+receiver+' .wrap img')[0]).attr('src');
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
                  document.getElementById('is_scroll_'+receiver).value = 1;
                  if(messages['chatroom_id']){
                      document.getElementById('sendButton').setAttribute('data-chatroom_id',messages['chatroom_id']);
                  }
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

  $(window).on('keydown', function(e) {
    if(e.which == 13 && $('#sendTextMessage').length > 0){
      $('#sendButton').click();
      return false;
    }
  });
</script>
@stop
