<footer id="contact">
  <div class="footer" >
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
          <h3> organizations</h3>
          <ul>
            @if(is_object($subdomain))
              <li class="" title="Main Site"><a href="{{ $subdomain->institute_url }}" target="_blank">Main Site</a></li>
            @endif
            <li title="Home"><a href="/"> Home</a></li>
            <li title="Courses"><a href="{{ url('online-courses') }}" >Courses</a></li>
            <li title="Test Series"><a href="{{ url('online-tests') }}" >Test Series</a></li>
            <li title="Admin Log in"><a href="{{ url('client/login') }}" >Admin Log in</a></li>
          </ul>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
          <h3> Contact Us </h3>
          <h4>
            @if(is_object($subdomain))
              {!! $subdomain->contact_us !!}
            @endif
         </h4>

       </div>
     </div>
     <!--/.row-->
   </div>
 </div>
 <div class="footer-bottom">
    <div class="container">
      <div class="row">
        <div class="col-lg-4  col-md-4 col-sm-6 col-xs-6">
          <p class="pull-left " title="vchiptech.com"><a href="http://www.vchiptech.com/" class="site_link" target="_blank"> vchiptech.com </a></p>
        </div>
        <div class="col-lg-4  col-md-4 col-sm-6 col-xs-6 text-center social-contact" >
          <ul class="social-network social-circle ">
            @if(is_object($subdomain))
              <li><a href="{{ $subdomain->facebook_url }}" class="icoFacebook" title="Facebook" target="_blank"><i class="fa fa-facebook"></i></a></li>
              <li><a href="{{ $subdomain->twitter_url }}" class="icoTwitter" title="Twitter" target="_blank"><i class="fa fa-twitter"></i></a></li>
              <li><a href="{{ $subdomain->google_url }}" class="icoGoogle" title="Google +" target="_blank"><i class="fa fa-google-plus"></i></a></li>
              <li><a href="{{ $subdomain->linkedin_url }}" class="icoLinkedin" title="Linkedin" target="_blank"><i class="fa fa-linkedin"></i></a></li>
            @endif
          </ul>
        </div>
        <div class="col-lg-4  col-md-4 col-sm-6 col-xs-12 ">
          @if(is_object($subdomain))
          <p class="pull-right" title="{{ $subdomain->institute_name }}"><a href="{{ $subdomain->institute_url }}" class="site_link" target="_blank">
              {{ $subdomain->institute_name }}
            </a></p>
          @endif
        </div>
      </div>
    </div>
  </div>
</footer>
<div id="loginUserModel" class="modal fade " role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header"  style="border-bottom: none;">
        <button class="close" data-dismiss="modal">×</button>
      </div>
      <div class="modal-body">
        <div class="modal-data">
            <div class="form-group">
              <input id="email" name="email" type="email" class="form-control" placeholder="vchip@gmail.com" autocomplete="off" required>
              <span class="help-block"></span>
            </div>
            <div class="form-group">
              <input id="password" name="password" type="password" class="form-control" placeholder="password" data-type="password" autocomplete="off" required >
              <span class="help-block"></span>
            </div>
            <div id="loginErrorMsg" class="hide">Wrong username or password</div>
            <button type="submit" value="login" name="submit" class="btn btn-info btn-block" onClick="loginUser();">Login</button>
            <br />
            <div class="form-group">
              <a href="{{ url('/auth/facebook') }}" class="btn btn-facebook btn-info btn-block" style="background-color: #3B5998; border-color: #3B5998;"><i class="fa fa-facebook"></i> Login </a>
            </div>
            <div class="form-group">
              <a href="{{ url('/auth/google') }}" class="btn btn-google btn-info btn-block" style="background-color: #DD4B39; border-color: #DD4B39;"><i class="fa fa-google"></i> Login </a>
            </div>
            <div class="form-group">
              <div class="col-md-12 control">
                  <div style="margin-top: 10px; margin-bottom: 20px;  color:#fff;" >
                      Need an account?
                  <a href="{{ url('/')}}" ">Sign Up</a>
                  </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@php
  $loginUser = Auth::guard('clientuser')->user();
@endphp
@if(is_object($loginUser))
<div class="panel-default popup-box popup-box-on" id="qnimate_{{$loginUser->id}}" style="right: 5px !important;">
  <div class="panel-heading top-bar" id="{{$loginUser->id}}" style="height:30px;">
    <div class="pull-left">
      <h3 class="panel-title">
        <b>{{$loginUser->name}}</b>
      <span id="message_header">
        <span class="badge" style="background-color: #f50909 !important;" id="msg_count_{{$subdomainName}}_{{$loginUser->client_id}}_{{$loginUser->id}}">0</span>
      </span>
      </h3>
    </div>
    <div class="pull-right">
      <a><span id="minim_chat_window_{{$loginUser->id}}" class="fa fa-minus icon_minim"></span></a>
    </div>
  </div>
  <div class="panel-body" id="{{$loginUser->id}}">
    <div class="popup-messages" id="{{$loginUser->client_id}}">
      <ul class="chat userchat" id="chatmessages_{{$loginUser->client_id}}_{{$loginUser->id}}">
      </ul>
    </div>
    <textarea id="message_{{$loginUser->id}}" data-receiver_id="{{$loginUser->client_id}}" placeholder="Type a message..." rows="3" cols="31" name="message" onfocus="readmessagecount(this);" style="width: 268px;"></textarea>
    <div class="">
      <button class="pull-right send-msg" id="send_{{$loginUser->id}}" data-send_id="{{$loginUser->id}}" data-chatroom_id="" onclick="sendMessage(this);">Send</button>
      <input type="hidden" id="message_limit_1" value="10">
      <input type="hidden" id="is_scroll_{{$loginUser->id}}" value="1">
      <input type="hidden" id="client_id" value="{{$loginUser->client_id}}">
      <input type="hidden" id="currentUserName" value="{{$loginUser->name}}">
      <input type="hidden" id="client_image" value="{{$loginUser->client->photo}}">
    </div>
  </div>
</div>
@endif
<script type="text/javascript">
  var socket = io.connect(window.location.protocol+'//'+window.location.host+':8080', { secure: true, reconnect: true, rejectUnauthorized : false, transports: ['websocket', 'polling'] });
  var full = window.location.host
  var parts = full.split('.')
  var subdomain = parts[0];

  function loginUser(){
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;
    if(email && password){
      $.ajax({
          method: "POST",
          url: "{{ url('clientUserLogin') }}",
          data: {email:email, password:password}
      })
      .done(function( msg ) {
        if('true' == msg){
          window.location.reload(true);
        } else {
          document.getElementById('loginErrorMsg').classList.remove('hide');
          if('Try after some time.' == msg){
            document.getElementById('loginErrorMsg').innerHTML = msg;
          } else {
            window.location.reload(true);
          }
        }
      });
    }
  }

  function changeType(ele){
    document.getElementById(ele).setAttribute('type', ele);
    document.getElementById('loginErrorMsg').classList.add('hide');
  }

  $(window).on('load', function(e){
    if (window.location.hash == '#_=_') {
      window.location.hash = ''; // for older browsers, leaves a # behind
      history.pushState('', document.title, window.location.pathname); // nice and clean
      e.preventDefault(); // no page reload
    }
    if(document.getElementById('user_id').value > 0){
      loadChat();
    }
    // receive message
    socket.on('clientMessage', function (data) {
        var roomArr = [];
        var current_user = document.getElementById('user_id').value;
        if(current_user ==  data.sender && 0 == data.created_by_client){
          roomArr.push(data.receiver);
          roomArr.push(data.sender);
        } else {
          roomArr.push(data.sender);
          roomArr.push(data.receiver);
        }
        var roomName = 'chatmessages_'+roomArr[0]+'_'+roomArr[1];
        var userchat = $('.userchat#'+roomName);
        var senderImgPath = data.senderImgPath;
        if('' == senderImgPath){
          senderImgPath = '/images/user1.png';
        }
        if(current_user ==  data.sender && 0 == data.created_by_client){
            var liEle = document.createElement('li');
            liEle.className = 'right clearfix addChat';
            liEle.innerHTML = '<span class="chat-img pull-right "><img src="'+senderImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+data.message+'</p></div><div class="chat-time clearfix"><span class="pull-right">'+messagteTime(data.created_at)+'</span></div>';
            userchat[0].appendChild(liEle);
        } else {
            var liEle = document.createElement('li');
            liEle.className = 'left clearfix addChat';
            liEle.innerHTML = '<span class="chat-img pull-left "><img src="'+senderImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+data.message+'</p></div><div class="chat-time clearfix"><span class="pull-left">'+messagteTime(data.created_at)+'</span></div>';
            userchat[0].appendChild(liEle);
        }
        $(userchat).parent().animate({scrollTop:$(userchat)[0].scrollHeight});
    });

    // client message count
    socket.on('clientMessageCount', function (data) {
        var totalMsgCount = document.getElementById('msg_count_'+subdomain+'_'+data.sender+'_'+data.receiver).innerHTML;
        if(totalMsgCount == 0){
          document.getElementById('msg_count_'+subdomain+'_'+data.sender+'_'+data.receiver).innerHTML = 1;
        } else {
          document.getElementById('msg_count_'+subdomain+'_'+data.sender+'_'+data.receiver).innerHTML = parseInt(totalMsgCount)+1;
        }
    });
  });
  // up & down chat users list
  $(document).on('click', '.top-bar', function (e) {
    var $this = $(this);
    if(!$this.hasClass('panel-collapsed')) {
      $this.parents('.panel').find('.panel-body').slideUp();
      $this.addClass('panel-collapsed');
      $('#minim_chat_window').removeClass('fa-minus').addClass('fa-plus');
      $this.parent().find('.panel-body').addClass('hide');
    } else {
      $this.parents('.panel').find('.panel-body').slideDown();
      $this.removeClass('panel-collapsed');
      $('#minim_chat_window').removeClass('fa-plus').addClass('fa-minus');
      $this.parent().find('.panel-body').removeClass('hide');
    }
  });
  // send & insert message
  function sendMessage(ele) {
      var id = $(ele).data('send_id');
      message = document.getElementById('message_'+id).value;
      clientId = document.getElementById('client_id').value;
      if(message != ''){
          var roomArr = [];
          roomArr.push(clientId);
          roomArr.push(document.getElementById('user_id').value);
          var room = 'private_'+subdomain+'_'+roomArr[0]+'_'+roomArr[1];
          var user = document.getElementById('currentUserName').value;
          var chatroomId = $(ele).data('chatroom_id');
          var receiver = clientId;
          var sender = document.getElementById('user_id').value;
          var token = "{{ csrf_token() }}";
          var created_at = new Date();
          var senderImgPath = $('.user-profile1').attr('src');
          var createdByClient = 0;
          socket.emit('sendClientUser', { room: room, message: message, user:user, sender:parseInt(sender) ,receiver:receiver, created_at:created_at,senderImgPath:senderImgPath,created_by_client:createdByClient});
          $.ajax({
              type: "POST",
              url: '{!! URL::to("sendMessage") !!}',
              dataType: "json",
              data: {'_token':token,'message':message,'sender':parseInt(sender) ,'receiver':receiver, 'chatroomId':chatroomId, 'created_by_client':createdByClient, 'client_id':clientId},
              success:function(data){
                  document.getElementById('message_'+id).value = '';
              }
          });
      }else{
          alert("Please Add Message.");
      }
  }

  // load chat
  function loadChat(){
    // add chat in to popup
    var token = "{{ csrf_token() }}";
    var messageLimit = 0;
    var receiverId = document.getElementById('client_id').value;
    var current_user = document.getElementById('user_id').value;
    var roomName = 'private_'+subdomain+'_'+receiverId+'_'+current_user;
    socket.emit('clientSubscribe', roomName);
    $.ajax({
      type: "POST",
      url: '{!! URL::to("clientPrivateChat") !!}',
      dataType: "json",
      data: {'_token':token,'receiver_id':receiverId, 'message_limit':messageLimit},
      success:function(messages){
          $('#message_limit_'+receiverId).val(10);
          var chatMessageId = document.getElementById('chatmessages_'+receiverId+'_'+current_user);
          var senderImgPath = $('.user-profile1').attr('src');
          if('' != $('#client_image').val()){
            var receiverImgPath = window.location.protocol+'//'+window.location.host+'/'+$('#client_image').val();
          } else {
            receiverImgPath = '/images/user1.png';
          }
          if('undefined' == typeof receiverImgPath || '' == receiverImgPath){
            receiverImgPath = '/images/user1.png';
          }
          $.each(messages['messages'],function(idx,obj){
            if(current_user == obj.sender_id && 0 == obj.created_by_client){
                $('#chatmessages_'+receiverId+'_'+current_user).prepend('<li class="right clearfix addChat"><span class="chat-img pull-right "><img src="'+senderImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+obj.message+'</p></div><div class="chat-time clearfix"><span class="pull-right">'+messagteTime(obj.created_at)+'</span></div></li>');
            } else {
                $('#chatmessages_'+receiverId+'_'+current_user).prepend('<li class="left clearfix addChat"><span class="chat-img pull-left "><img src="'+receiverImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+obj.message+'</p></div><div class="chat-time clearfix"><span class="pull-left">'+messagteTime(obj.created_at)+'</span></div></li>');
            }
          });
          if(messages['unreadCount'] && messages['unreadCount'][0]['unread']){
              document.getElementById('msg_count_'+subdomain+'_'+receiverId+'_'+current_user).innerHTML = messages['unreadCount'][0]['unread'];
          }
          $(chatMessageId).parent().animate({scrollTop:$(chatMessageId)[0].scrollHeight});
          if(messages['chatroom_id']){
              document.getElementById('send_'+current_user).setAttribute('data-chatroom_id',messages['chatroom_id']);
          }
          // document.getElementById('message_'+current_user).focus();
      }
    });
  }
  $('.popup-messages').scroll(function() {
    if($(this).scrollTop() == 0){
      getNextchatMessages($(this).attr('id'));
    }
  });
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

  // make count zero
  function readmessagecount(ele){
    var receiver = $(ele).attr('data-receiver_id');
    var current_user = document.getElementById('user_id').value;
    var token = "{{ csrf_token() }}";
    if(parseInt(document.getElementById('msg_count_'+subdomain+'_'+receiver+'_'+current_user).innerHTML)){
      document.getElementById('msg_count_'+subdomain+'_'+receiver+'_'+current_user).innerHTML = '';
      $.ajax({
        type: "POST",
        url: '{!! URL::to("readClientChatMessages") !!}',
        dataType: "json",
        data: {'_token':token, 'sender_id':receiver},
        success:function(msg){}
      });
    }
  }

 // show next 10 messages
  function getNextchatMessages(receiverId){
    var current_user = document.getElementById('user_id').value;
    var roomArr = [];
    roomArr.push(receiverId);
    roomArr.push(current_user);
    if(1 == document.getElementById('is_scroll_'+current_user).value){
      // add chat in to popup
      var token = "{{ csrf_token() }}";
      var messageLimit = parseInt($('#message_limit_'+receiverId).val())+10;
      $.ajax({
          type: "POST",
          url: '{!! URL::to("clientPrivateChat") !!}',
          dataType: "json",
          data: {'_token':token,'receiver_id':receiverId, 'message_limit':messageLimit},
          success:function(messages){
              $('#message_limit_'+receiverId).val(messageLimit);
              var chatMessageId = document.getElementById('chatmessages_'+roomArr[0]+'_'+roomArr[1]);
              if(messages['messages'].length > 0){
                var senderImgPath = $('.user-profile1').attr('src');
                var receiverImgPath = window.location.protocol+'//'+window.location.host+'/'+$('#client_image').val();
                if('undefined' == typeof receiverImgPath){
                  receiverImgPath = '/images/user1.png';
                }
                  $.each(messages['messages'],function(idx,obj){
                      if(current_user == obj.sender_id && 0 == obj.created_by_client){
                          $('#chatmessages_'+roomArr[0]+'_'+roomArr[1]).prepend('<li class="right clearfix addChat"><span class="chat-img pull-right "><img src="'+senderImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+obj.message+'</p></div><div class="chat-time clearfix"><span class="pull-right">'+messagteTime(obj.created_at)+'</span></div></li>');
                      } else {
                          $('#chatmessages_'+roomArr[0]+'_'+roomArr[1]).prepend('<li class="left clearfix addChat"><span class="chat-img pull-left "><img src="'+receiverImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+obj.message+'</p></div><div class="chat-time clearfix"><span class="pull-left">'+messagteTime(obj.created_at)+'</span></div></li>');
                      }
                  });
                  $(chatMessageId).parent().animate({scrollTop:100});
                  if(messages['chatroom_id']){
                      document.getElementById('send_'+current_user).setAttribute('data-chatroom_id',messages['chatroom_id']);
                  }
                  document.getElementById('is_scroll_'+current_user).value = 1;
              } else {
                  document.getElementById('is_scroll_'+current_user).value = 0;
              }
          }
      });
    }
  }

  $(window).on('keydown', function(e) {
    var senderId = document.getElementById('user_id').value;
    if (e.which == 13 && senderId > 0) {
      $('#send_'+senderId).click();
      return false;
    }
  });
</script>