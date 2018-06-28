@php
  $loginUser = Auth::user();
@endphp
@if($loginUser)
  <input type="hidden" id="currentUser" value="{{ $loginUser->id }}">
  <input type="hidden" id="currentUserName" value="{{ $loginUser->name }}">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <div class="row chat-window " id="chat_window_1" style="margin-left:10px;">
          <div class="col-xs-12 col-md-12">
            <div class="panel panel-default">
              <div class="panel-heading top-bar">
                <div class="pull-left">
                  <h3 class="panel-title"><span class="fa fa-comments"></span> <b>Messaging</b>
                    <span id="message_header">
                      <span class="badge" style="background-color: #f50909 !important;" id="msg_count_1_{{ $loginUser->id }}"></span>
                    </span>
                  </h3>
                </div>
                <div class="pull-right" >
                  <a ><span id="minim_chat_window" class="fa fa-minus icon_minim"></span></a>
                </div>
              </div>
              <div id="search">
                <input type="text" id="search_contact" name="student" class="form-control" placeholder="Search contacts..." onkeyup="searchContact(this.value,false);" style="color: black; background: #ddd;">
              </div>
              <div id="chatAdmin">
                @if('ceo@vchiptech.com' != $loginUser->email)
                  <li class="left clearfix addChat" id="{{$chatAdminId}}" data-user_name="Vchip Admin" onclick="showChat(this);" style="list-style: none;padding-left: 13px;border-bottom: 1px dotted #B3A9A9;">
                    <span class="chat-img pull-left">
                      <img src="{{asset('images/logo/logo.jpg')}}" alt="User Avatar" class="img-circle">
                    </span>
                    <div class="chat-body clearfix" style="padding-left: 60px;">
                      <div class="header">
                        <strong class="primary-font">Vchip Admin </strong>
                          <span id="unread_{{ $loginUser->id }}_{{$chatAdminId}}" style="color: red;"></span>
                          <span class="chat-img pull-right" style="padding-right: 30px;">
                          @if($chatAdminLive)
                            <img src="/images/online.png" id="userstatus_{{$chatAdminId}}" data-user_id="{{$chatAdminId}}" style="height:  20px; width: 20px;">
                          @else
                            <img src="/images/offline.png" id="userstatus_{{$chatAdminId}}" data-user_id="{{$chatAdminId}}" style="height:  20px; width: 20px;">
                          @endif
                        </span>
                      </div>
                      <p>Vchip</p>
                    </div>
                  </li>
                @endif
                <input type="hidden" id="chat_admin_id" value="{{$chatAdminId}}">
              </div>
              <div class="panel-body">
                <ul class="chat" id="chat_users">
                </ul>
                <input type="hidden" id="isUserScroll" value="0">
                <input type="hidden" id="previuos_chat_users" value="">
              </div>
            </div>
          </div>
        </div>
        <div class="" id="userchatwindow" style="">
        </div>
      </div>
    </div>
  </div>
  <script>
    //this function can remove a array element.
    Array.remove = function(array, from, to) {
        var rest = array.slice((to || from) + 1 || array.length);
        array.length = from < 0 ? array.length + from : from;
        return array.push.apply(array, rest);
    };
    //this variable represents the total number of popups can be displayed according to the viewport width
    var total_popups = 0;
    //arrays of popups ids
    var popups = [];
    //this is used to close a popup
    function close_popup(id)
    {
        for(var iii = 0; iii < popups.length; iii++)
        {
            if(id == popups[iii])
            {
                Array.remove(popups, iii);
                // document.getElementById(id).style.display = "none";
                document.getElementById(id).classList.remove('popup-box-on');
                calculate_popups();
                return;
            }
        }
    }
    //displays the popups. Displays based on the maximum number of popups that can be displayed on the current viewport width
    function display_popups()
    {
        var right = 302;
        var iii = 0;
        for(iii; iii < total_popups; iii++)
        {
            if(popups[iii] != undefined)
            {
                var element = document.getElementById(popups[iii]);
                element.style.right = right + "px";
                right = right + 302;
                element.classList.add('popup-box-on');
            }
        }
        for(var jjj = iii; jjj < popups.length; jjj++)
        {
            var elementHide = document.getElementById(popups[jjj]);
            elementHide.classList.add('popup-box-on');
            elementHide.style.right = "10px";
        }
    }
    //creates markup for a new popup. Adds the id to popups array.
    function register_popup(id)
    {
        for(var iii = 0; iii < popups.length; iii++)
        {
            //already registered. Bring it to front.
            if(id == popups[iii])
            {
                Array.remove(popups, iii);
                popups.unshift(id);
                calculate_popups();
                return;
            }
        }
        popups.unshift(id);
        calculate_popups();
    }
    //calculate the total number of popups suitable and then populate the toatal_popups variable.
    function calculate_popups()
    {
        var width = window.innerWidth;
        if(width < 605)
        {
          total_popups = 0;
        }
        else
        {
          width = width - 200;
          //320 is width of a single popup box
          total_popups = parseInt(width/302);
        }
        display_popups();
    }
    //recalculate when window is loaded and also when window is resized.
    window.addEventListener("resize", calculate_popups);
    window.addEventListener("load", calculate_popups);
  </script>
  <script type="text/javascript">
      var socket = io.connect(window.location.protocol+'//'+window.location.host+':8080', { secure: true, reconnect: true, rejectUnauthorized : false, transports: ['websocket', 'polling'] });

      // show user chat messages
      function showChat(ele){
          var receiverId = $(ele).attr('id');
          var receiverName = $(ele).data('user_name');
          var current_user = document.getElementById('user_id').value;
          var roomArr = [];
          roomArr.push(receiverId);
          roomArr.push(current_user);
          var roomMembers = roomArr.sort();
          var roomName = 'private_'+roomArr[0]+'_'+roomArr[1];
          if(document.getElementById('qnimate_'+receiverId)){
            document.getElementById('qnimate_'+receiverId).classList.add('popup-box-on');
            document.getElementById('qnimate_'+receiverId).children[1].style.display = "block";
            // set popup position
            register_popup('qnimate_'+receiverId);
            return false;
          }
          // add chat window
          divChatWindows = document.getElementById('userchatwindow');

          var popupBoxDiv = document.createElement('div');
          popupBoxDiv.className = 'panel-default popup-box';
          popupBoxDiv.id = 'qnimate_'+receiverId;

          var popupHeadingDiv = document.createElement('div');
          popupHeadingDiv.className = 'panel-heading top-bar';
          popupHeadingDiv.id = receiverId;
          popupHeadingDiv.setAttribute('style','height:30px;');

          var popupNameDiv = document.createElement('div');
          popupNameDiv.className = 'pull-left';
          popupNameDiv.innerHTML = '<h3 class="panel-title"><b>'+receiverName+'</b></h3>';
          popupHeadingDiv.appendChild(popupNameDiv);

          var popupCloseDiv = document.createElement('div');
          popupCloseDiv.className = 'pull-right';
          popupCloseDiv.innerHTML = '<a><span id="minim_chat_window_'+receiverId+'" class="fa fa-minus icon_minim"></span></a><a data-widget="remove" id="'+receiverId+'" class="chat-header-button pull-right" type="button" onclick="closeChat(this);"><i class="fa fa-remove"></i></a>';
          popupHeadingDiv.appendChild(popupCloseDiv);
          popupBoxDiv.appendChild(popupHeadingDiv);

          var popupBodyDiv = document.createElement('div');
          popupBodyDiv.className = 'panel-body';
          popupBodyDiv.id = receiverId;

          var popupMessagesDiv = document.createElement('div');
          popupMessagesDiv.className = 'popup-messages';
          popupMessagesDiv.id = receiverId;
          popupMessagesDiv.innerHTML = '<ul class="chat userchat" id="'+'chatmessages_'+roomArr[0]+'_'+roomArr[1]+'"></ul>';
          popupBodyDiv.appendChild(popupMessagesDiv);

          var popupBodyText = document.createElement('textarea');
          popupBodyText.id = 'message_'+receiverId;
          popupBodyText.setAttribute('data-receiver_id', receiverId);
          popupBodyText.setAttribute('placeholder', 'Type a message...');
          popupBodyText.setAttribute('rows', '3');
          popupBodyText.setAttribute('cols', '31');
          popupBodyText.setAttribute('name', 'message');
          popupBodyText.setAttribute('onfocus', 'readmessagecount(this);');
          popupBodyText.setAttribute('style', 'width: 268px;');
          popupBodyDiv.appendChild(popupBodyText);

          var popupFooterDiv = document.createElement('div');
          popupFooterDiv.className = '';
          popupFooterDiv.innerHTML = '<button class="pull-right send-msg" id="send_'+receiverId+'" data-send_id="'+receiverId+'" data-chatroom_id="" onclick="sendMessage(this);">Send</button><input type="hidden" id="message_limit_'+receiverId+'" value="0"><input type="hidden" id="is_scroll_'+receiverId+'" value="1">';
          popupBodyDiv.appendChild(popupFooterDiv);
          popupBoxDiv.appendChild(popupBodyDiv);
          divChatWindows.appendChild(popupBoxDiv);

          $('.popup-messages').scroll(function() {
              if($(this).scrollTop() == 0){
                getNextchatMessages($(this).attr('id'));
              }
          });

          // set popup position
          register_popup('qnimate_'+receiverId);
          socket.emit('subscribe', roomName);
          // add chat in to popup
          var token = "{{ csrf_token() }}";
          var messageLimit = 0;
          $.ajax({
              type: "POST",
              url: '{!! URL::to("privateChat") !!}',
              dataType: "json",
              data: {'_token':token,'receiver_id':receiverId, 'message_limit':messageLimit},
              success:function(messages){
                  $('#message_limit_'+receiverId).val(10);
                  var chatMessageId = document.getElementById('chatmessages_'+roomArr[0]+'_'+roomArr[1]);
                  var senderImgPath = $('#currentUserImage').attr('src');
                  var receiverImgPath = $($('li#'+receiverId+' span img')[0]).attr('src');
                  if('undefined' == typeof receiverImgPath){
                    receiverImgPath = '/images/user1.png';
                  }
                  $.each(messages['messages'],function(idx,obj){
                    var textMessage = obj.message.replace(/<[^>]+>/ig, '');
                    if(current_user == obj.sender_id){
                        $('#chatmessages_'+roomArr[0]+'_'+roomArr[1]).prepend('<li class="right clearfix addChat"><span class="chat-img pull-right "><img src="'+senderImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+textMessage+'</p></div><div class="chat-time clearfix"><span class="pull-right">'+messagteTime(obj.created_at)+'</span></div></li>');
                    } else {
                        $('#chatmessages_'+roomArr[0]+'_'+roomArr[1]).prepend('<li class="left clearfix addChat"><span class="chat-img pull-left "><img src="'+receiverImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+textMessage+'</p></div><div class="chat-time clearfix"><span class="pull-left">'+messagteTime(obj.created_at)+'</span></div></li>');
                    }
                  });
                  $(chatMessageId).parent().animate({scrollTop:$(chatMessageId)[0].scrollHeight});
                  if(messages['chatroom_id']){
                      document.getElementById('send_'+receiverId).setAttribute('data-chatroom_id',messages['chatroom_id']);
                  }
                  document.getElementById('message_'+receiverId).focus();
              }
          });
      }

      $('#chat_window_1 .panel-body').scroll(function() {
          if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
             if(0 == $('#isUserScroll').val()){
                 var limitStart = $("#chat_users li").length;
                 $('#isUserScroll').val(1);
                 loadChatUsers(limitStart);
             }
          }
      });

      function searchContact(contact, showName){
        if(contact.length > 2){
          $.ajax({
            method: "POST",
            url: "{{url('searchContact')}}",
            data:{contact:contact}
          })
          .done(function( users ) {
            var current_user = document.getElementById('user_id').value;
            var chatUsers = document.getElementById('chat_users');
            chatUsers.innerHTML = '';
            $.each(users['users'],function(idx,obj){
              var liEle = document.createElement('li');
              if(document.getElementById('user_id').value == obj['id']){
                  liEle.className = 'left clearfix addChat';
              } else {
                  liEle.className = 'left clearfix addChat';
              }
              liEle.id = obj['id'];
              liEle.setAttribute('data-user_name', obj['name']);
              liEle.setAttribute('onclick', 'showChat(this);');

              var spanImage = document.createElement('span');
              spanImage.className = 'chat-img pull-left';
              if(obj['photo']){
                if(obj['photo'].indexOf("userStorage") !== -1){
                    var userImagePath = window.location.protocol+'//'+window.location.host+"/"+obj['photo'];
                  } else {
                    var userImagePath = obj['photo'];
                  }
              } else {
                var userImagePath = "/images/user1.png";
              }
              spanImage.innerHTML = '<img src="'+userImagePath+'" alt="User Avatar" class="img-circle" />';
              liEle.appendChild(spanImage);

              var divChatBody = document.createElement('div');
              divChatBody.className = 'chat-body clearfix';

              var divHeader = document.createElement('div');
              divHeader.className = 'header';

              var strongName = document.createElement('strong');
              strongName.className = 'primary-font';
              strongName.innerHTML = obj['name'] + ' ';
              divHeader.appendChild(strongName);

              var spanUnread = document.createElement('span');
              spanUnread.id = 'unread_'+current_user+'_'+obj['id'];
              spanUnread.setAttribute('style','color: red;');
              if(users['unreadCount'] && users['unreadCount'][obj.id] > 0){
                spanUnread.innerHTML = ' ' + users['unreadCount'][obj.id];
                if(parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML) > 0){
                  document.getElementById('msg_count_1_'+current_user).innerHTML = parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML)+parseInt(users['unreadCount'][obj.id]);
                } else {
                  document.getElementById('msg_count_1_'+current_user).innerHTML = 0+parseInt(users['unreadCount'][obj.id]);
                }
                document.getElementById('msg_count_2_'+current_user).innerHTML = parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML);
                if(parseInt(document.getElementById('userCnt_'+current_user).innerHTML) > 0)  {
                  document.getElementById('userCnt_'+current_user).innerHTML = parseInt(document.getElementById('userCnt_'+current_user).innerHTML) + parseInt(users['unreadCount'][obj.id]);
                } else {
                  document.getElementById('userCnt_'+current_user).innerHTML = 0 + parseInt(users['unreadCount'][obj.id]);
                }
              }
              divHeader.appendChild(spanUnread);

              var spanStatus = document.createElement('span');
              spanStatus.className = 'chat-img pull-right';
              if(users['onlineUsers'] && users['onlineUsers'][obj['id']]){
                  spanStatus.innerHTML += '<img src="/images/online.png" id="userstatus_'+obj['id']+'" data-user_id="'+obj['id']+'" style="height:  20px; width: 20px;" />';
              } else {
                  spanStatus.innerHTML += '<img src="/images/offline.png" id="userstatus_'+obj['id']+'" data-user_id="'+obj['id']+'" style="height:  20px; width: 20px;" />';
              }
              divHeader.appendChild(spanStatus);
              divChatBody.appendChild(divHeader);

              var pEle = document.createElement('p');
              var collegeStr = obj['college'];
              pEle.innerHTML = collegeStr.substring(0, 15);
              divChatBody.appendChild(pEle);
              liEle.appendChild(divChatBody);
              chatUsers.appendChild(liEle);

              var roomArr = [];
              roomArr.push(obj['id']);
              roomArr.push(current_user);
              var roomMembers = roomArr.sort();
              var roomName = 'private_'+roomArr[0]+'_'+roomArr[1];
              socket.emit('subscribe', roomName);
            });
          });
        } else if('' == contact){
          showChatUsers();
        }
      }

      // make count zero
      function readmessagecount(ele){
        var receiver = $(ele).attr('data-receiver_id');
        var current_user = document.getElementById('user_id').value;
        var token = "{{ csrf_token() }}";
        if(parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML) > 0 && parseInt(document.getElementById('unread_'+current_user+'_'+receiver).innerHTML) > 0){
          document.getElementById('msg_count_1_'+current_user).innerHTML -= parseInt(document.getElementById('unread_'+current_user+'_'+receiver).innerHTML);
          document.getElementById('msg_count_2_'+current_user).innerHTML = parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML);
          if(parseInt(document.getElementById('userCnt_'+current_user).innerHTML) > 0)  {
            document.getElementById('userCnt_'+current_user).innerHTML = parseInt(document.getElementById('userCnt_'+current_user).innerHTML) - parseInt(document.getElementById('unread_'+current_user+'_'+receiver).innerHTML);
          }

          $.ajax({
            type: "POST",
            url: '{!! URL::to("readChatMessages") !!}',
            dataType: "json",
            data: {'_token':token, 'sender_id':receiver},
            success:function(msg){}
          });
        }
        if(parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML) == 0){
          document.getElementById('msg_count_1_'+current_user).innerHTML = '';
          document.getElementById('msg_count_2_'+current_user).innerHTML = 0;
        }
        document.getElementById('unread_'+current_user+'_'+receiver).innerHTML = '';
      }

      // show chat users
      function loadChatUsers(limitStart){
          var current_user = document.getElementById('user_id').value;
          var chatAdminId = document.getElementById('chat_admin_id').value;
          var previuosChatUsers = document.getElementById('previuos_chat_users').value;
          var token = "{{ csrf_token() }}";
          $.ajax({
            type: "POST",
            url: '{!! URL::to("loadChatUsers") !!}',
            dataType: "json",
            data: {'_token':token, 'limit_start':limitStart, 'previuos_chat_users':previuosChatUsers},
            success:function(users){
              if(users['chatusers'].length > 0){
                // renderChatUsers(users)
                var chatUsers = document.getElementById('chat_users');
                $.each(users['chatusers'],function(idx,obj){
                    var liEle = document.createElement('li');
                    if(document.getElementById('user_id').value == obj['id']){
                        liEle.className = 'hide left clearfix addChat';
                    } else {
                        liEle.className = 'left clearfix addChat';
                    }
                    liEle.id = obj['id'];
                    liEle.setAttribute('data-user_name', obj['name']);
                    liEle.setAttribute('onclick', 'showChat(this);');

                    var spanImage = document.createElement('span');
                    spanImage.className = 'chat-img pull-left';
                    if(obj['photo']){
                      if(obj['photo'].indexOf("userStorage") !== -1){
                        var userImagePath = window.location.protocol+'//'+window.location.host+"/"+obj['photo'];
                      } else {
                        var userImagePath = obj['photo'];
                      }
                    } else {
                      var userImagePath = "/images/user1.png";
                    }

                    spanImage.innerHTML = '<img src="'+userImagePath+'" alt="User Avatar" class="img-circle" />';
                    liEle.appendChild(spanImage);

                    var divChatBody = document.createElement('div');
                    divChatBody.className = 'chat-body clearfix';

                    var divHeader = document.createElement('div');
                    divHeader.className = 'header';

                    var strongName = document.createElement('strong');
                    strongName.className = 'primary-font';
                    strongName.innerHTML = obj['name'];
                    divHeader.appendChild(strongName);

                    var spanUnread = document.createElement('span');
                    spanUnread.id = 'unread_'+current_user+'_'+obj['id'];
                    spanUnread.setAttribute('style','color: red;');
                    if(users['unreadCount'][obj.id] > 0){
                      spanUnread.innerHTML = ' ' + users['unreadCount'][obj.id];
                      if(parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML) > 0){
                        document.getElementById('msg_count_1_'+current_user).innerHTML = parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML)+parseInt(users['unreadCount'][obj.id]);
                      } else {
                        document.getElementById('msg_count_1_'+current_user).innerHTML = 0+parseInt(users['unreadCount'][obj.id]);
                      }
                        document.getElementById('msg_count_2_'+current_user).innerHTML = parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML);
                        if(parseInt(document.getElementById('userCnt_'+current_user).innerHTML) > 0)  {
                          document.getElementById('userCnt_'+current_user).innerHTML = parseInt(document.getElementById('userCnt_'+current_user).innerHTML) + parseInt(users['unreadCount'][obj.id]);
                        } else {
                          document.getElementById('userCnt_'+current_user).innerHTML = 0 + parseInt(users['unreadCount'][obj.id]);
                        }
                    }
                    divHeader.appendChild(spanUnread);

                    var spanStatus = document.createElement('span');
                    spanStatus.className = 'chat-img pull-right';
                    if(users['onlineUsers'] && users['onlineUsers'][obj['id']]){
                        spanStatus.innerHTML += '<img src="/images/online.png" id="userstatus_'+obj['id']+'" data-user_id="'+obj['id']+'" style="height:  20px; width: 20px;" />';

                    } else {
                        spanStatus.innerHTML += '<img src="/images/offline.png" id="userstatus_'+obj['id']+'" data-user_id="'+obj['id']+'" style="height:  20px; width: 20px;" />';
                    }
                    divHeader.appendChild(spanStatus);
                    divChatBody.appendChild(divHeader);

                    var pEle = document.createElement('p');
                    var collegeStr = obj['college'];
                    pEle.innerHTML = collegeStr.substring(0, 15);
                    divChatBody.appendChild(pEle);
                    liEle.appendChild(divChatBody);
                    chatUsers.appendChild(liEle);

                    var roomArr = [];
                    roomArr.push(obj['id']);
                    roomArr.push(current_user);
                    var roomMembers = roomArr.sort();
                    var roomName = 'private_'+roomArr[0]+'_'+roomArr[1];
                    socket.emit('subscribe', roomName);
                });
                if(chatAdminId != current_user){
                  var roomArr = [];
                    roomArr.push(chatAdminId);
                    roomArr.push(current_user);
                    var roomMembers = roomArr.sort();
                    var roomName = 'private_'+roomArr[0]+'_'+roomArr[1];
                    socket.emit('subscribe', roomName);
                }
                $('#isUserScroll').val(0);

              } else {
                $('#isUserScroll').val(1);
              }
            }
          });
      }

      // show chat users
      function showChatUsers(){
        var token = "{{ csrf_token() }}";
        $.ajax({
          type: "POST",
          url: '{!! URL::to("showChatUsers") !!}',
          dataType: "json",
          data: {'_token':token},
          success:function(users){
            renderChatUsers(users);
          }
        });
      }

      function renderChatUsers(users){
        var current_user = document.getElementById('user_id').value;
        var chatUsers = document.getElementById('chat_users');
        var chatAdminId = document.getElementById('chat_admin_id').value;
        chatUsers.innerHTML = '';
        document.getElementById('previuos_chat_users').value = users['chatusers']['chat_users'];
        $.each(users['chatusers']['users'],function(idx,obj){
          var liEle = document.createElement('li');
          if(document.getElementById('user_id').value == obj['id']){
              liEle.className = 'left clearfix addChat hide';
          } else {
              liEle.className = 'left clearfix addChat';
          }
          liEle.id = obj['id'];
          liEle.setAttribute('data-user_name', obj['name']);
          liEle.setAttribute('onclick', 'showChat(this);');

          var spanImage = document.createElement('span');
          spanImage.className = 'chat-img pull-left';
          if(obj['photo']){
            if(obj['photo'].indexOf("userStorage") !== -1){
                var userImagePath = window.location.protocol+'//'+window.location.host+"/"+obj['photo'];
              } else {
                var userImagePath = obj['photo'];
              }
          } else {
            var userImagePath = "/images/user1.png";
          }
          spanImage.innerHTML = '<img src="'+userImagePath+'" alt="User Avatar" class="img-circle" />';
          liEle.appendChild(spanImage);

          var divChatBody = document.createElement('div');
          divChatBody.className = 'chat-body clearfix';

          var divHeader = document.createElement('div');
          divHeader.className = 'header';

          var strongName = document.createElement('strong');
          strongName.className = 'primary-font';
          strongName.innerHTML = obj['name'] + ' ';
          divHeader.appendChild(strongName);

          var spanUnread = document.createElement('span');
          spanUnread.id = 'unread_'+current_user+'_'+obj['id'];
          spanUnread.setAttribute('style','color: red;');
          if(users['unreadCount'] && users['unreadCount'][obj.id] > 0){
            spanUnread.innerHTML = ' ' + users['unreadCount'][obj.id];
            if(parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML) > 0){
              document.getElementById('msg_count_1_'+current_user).innerHTML = parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML)+parseInt(users['unreadCount'][obj.id]);
            } else {
              document.getElementById('msg_count_1_'+current_user).innerHTML = 0+parseInt(users['unreadCount'][obj.id]);
            }
            document.getElementById('msg_count_2_'+current_user).innerHTML = parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML);
            if(parseInt(document.getElementById('userCnt_'+current_user).innerHTML) > 0)  {
              document.getElementById('userCnt_'+current_user).innerHTML = parseInt(document.getElementById('userCnt_'+current_user).innerHTML) + parseInt(users['unreadCount'][obj.id]);
            } else {
              document.getElementById('userCnt_'+current_user).innerHTML = 0 + parseInt(users['unreadCount'][obj.id]);
            }
          }
          divHeader.appendChild(spanUnread);

          var spanStatus = document.createElement('span');
          spanStatus.className = 'chat-img pull-right';
          if(users['onlineUsers'] && users['onlineUsers'][obj['id']]){
              spanStatus.innerHTML += '<img src="/images/online.png" id="userstatus_'+obj['id']+'" data-user_id="'+obj['id']+'" style="height:  20px; width: 20px;" />';

          } else {
              spanStatus.innerHTML += '<img src="/images/offline.png" id="userstatus_'+obj['id']+'" data-user_id="'+obj['id']+'" style="height:  20px; width: 20px;" />';
          }
          divHeader.appendChild(spanStatus);
          divChatBody.appendChild(divHeader);

          var pEle = document.createElement('p');
          var collegeStr = obj['college'];
          pEle.innerHTML = collegeStr.substring(0, 15);
          divChatBody.appendChild(pEle);
          liEle.appendChild(divChatBody);
          chatUsers.appendChild(liEle);

          var roomArr = [];
          roomArr.push(obj['id']);
          roomArr.push(current_user);
          var roomMembers = roomArr.sort();
          var roomName = 'private_'+roomArr[0]+'_'+roomArr[1];
          socket.emit('subscribe', roomName);
        });
        if(chatAdminId != current_user){
          var roomArr = [];
          roomArr.push(chatAdminId);
          roomArr.push(current_user);
          var roomMembers = roomArr.sort();
          var roomName = 'private_'+roomArr[0]+'_'+roomArr[1];
          socket.emit('subscribe', roomName);
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

      function closeChat(ele){
        var id = $(ele).attr('id');
        close_popup('qnimate_'+id);
      }

      // up & down chat users list
      $(document).on('click', '#chat_window_1 .top-bar', function (e) {
        var $this = $(this);
        if(!$this.hasClass('panel-collapsed')) {
          $this.parents('.panel').find('.panel-body').slideUp();
          $this.addClass('panel-collapsed');
          $('#minim_chat_window').removeClass('fa-minus').addClass('fa-plus');
          $('#search').addClass('hide');
          $('#chatAdmin').addClass('hide');
        } else {
          $this.parents('.panel').find('.panel-body').slideDown();
          $this.removeClass('panel-collapsed');
          $('#minim_chat_window').removeClass('fa-plus').addClass('fa-minus');
          $('#search').removeClass('hide');
          $('#chatAdmin').removeClass('hide');
        }
      });

      // up & down chat window
      $(document).on('click', '#userchatwindow .top-bar', function (e) {
        var $this = $(this);
        var userId = $(this).attr('id');
        if(!$this.hasClass('panel-collapsed')) {
          $this.parents('#qnimate_'+userId).find('.panel-body').slideUp();
          $this.addClass('panel-collapsed');
          $('#minim_chat_window_'+userId).removeClass('fa-minus').addClass('fa-plus');
        } else {
          $this.parents('#qnimate_'+userId).find('.panel-body').slideDown();
          $this.removeClass('panel-collapsed');
          $('#minim_chat_window_'+userId).removeClass('fa-plus').addClass('fa-minus');
        }
      });
      // show next 10 messages
      function getNextchatMessages(receiverId){
          var current_user = document.getElementById('user_id').value;
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
                  url: '{!! URL::to("privateChat") !!}',
                  dataType: "json",
                  data: {'_token':token,'receiver_id':receiverId, 'message_limit':messageLimit},
                  success:function(messages){
                      $('#message_limit_'+receiverId).val(messageLimit);
                      var chatMessageId = document.getElementById('chatmessages_'+roomArr[0]+'_'+roomArr[1]);
                      if(messages['messages'].length > 0){
                        var senderImgPath = $('#currentUserImage').attr('src');
                        var receiverImgPath = $($('li#'+receiverId+' span img')[0]).attr('src');
                          $.each(messages['messages'],function(idx,obj){
                            var textMessage = obj.message.replace(/<[^>]+>/ig, '');
                              if(current_user == obj.sender_id){
                                  $('#chatmessages_'+roomArr[0]+'_'+roomArr[1]).prepend('<li class="right clearfix addChat"><span class="chat-img pull-right "><img src="'+senderImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+textMessage+'</p></div><div class="chat-time clearfix"><span class="pull-right">'+messagteTime(obj.created_at)+'</span></div></li>');
                              } else {
                                  $('#chatmessages_'+roomArr[0]+'_'+roomArr[1]).prepend('<li class="left clearfix addChat"><span class="chat-img pull-left "><img src="'+receiverImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+textMessage+'</p></div><div class="chat-time clearfix"><span class="pull-left">'+messagteTime(obj.created_at)+'</span></div></li>');
                              }
                          });
                          $(chatMessageId).parent().animate({scrollTop:100});
                          if(messages['chatroom_id']){
                              document.getElementById('send_'+receiverId).setAttribute('data-chatroom_id',messages['chatroom_id']);
                          }
                          document.getElementById('is_scroll_'+receiverId).value = 1;
                      } else {
                          document.getElementById('is_scroll_'+receiverId).value = 0;
                      }
                  }
              });
          }
      }

      $(document).ready(function () {
        if(document.getElementById('user_id').value > 0){
          showChatUsers();
          setInterval(checkOnlineUsers, 180000);
        }
        $('#chat_window_1 .top-bar').click();
      });

      function checkOnlineUsers(){
          var token = "{{ csrf_token() }}";
          var current_user = document.getElementById('user_id').value;
          $.ajax({
              type: "POST",
              url: '{!! URL::to("checkOnlineUsers") !!}',
              dataType: "json",
              data: {'_token':token},
              success:function(onlineusers){
                if(onlineusers){
                  var messageUsers = $('img[id^=userstatus_]');
                  $.each(messageUsers, function(idx, obj){
                      var userId = $(obj).data('user_id');
                      if(current_user != userId){
                          var userStatus = document.getElementById('userstatus_'+userId);
                          if(onlineusers[userId]){
                              userStatus.setAttribute('src', '/images/online.png');
                          } else {
                              userStatus.setAttribute('src', '/images/offline.png');
                          }
                      }

                  });
                }
              }
          });
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
              var userchat = $('.userchat#'+roomName);
              console.log(document.getElementById('unread_'+data.sender));
              console.log(userchat.length);
              if( null == document.getElementById('unread_'+current_user+'_'+data.sender) && userchat.length > 0){
                var spanUnread = document.createElement('span');
                spanUnread.className = "hide";
                spanUnread.id = 'unread_'+current_user+'_'+data.sender;
                spanUnread.setAttribute('style','color: red;');
                spanUnread.value = 1;
                userchat[0].appendChild(spanUnread);
              }
              var unread = document.getElementById('unread_'+current_user+'_'+data.sender).innerHTML;
              console.log(unread);
              if(unread == ''){
                document.getElementById('unread_'+current_user+'_'+data.sender).innerHTML = 1;
              } else {
                document.getElementById('unread_'+current_user+'_'+data.sender).innerHTML = parseInt(unread)+1;;
              }
              var messageHeader = document.getElementById('message_header');
              if( null == document.getElementById('msg_count_1_'+data.receiver)){
                var spanCount = document.createElement('span');
                if(current_user == data.sender){
                  spanCount.className = "badge hide";
                } else {
                  spanCount.className = "badge";
                }
                spanCount.id = 'msg_count_1_'+data.receiver;
                spanCount.setAttribute('style','background-color: #f50909 !important;');
                messageHeader.appendChild(spanCount);
              }

              var allChatMessages = document.getElementById('all_chat_messages');
              if( null == document.getElementById('msg_count_2_'+data.receiver)){
                var spanCount = document.createElement('span');
                if(current_user == data.sender){
                  spanCount.className = "hide";
                }
                spanCount.id = 'msg_count_2_'+data.receiver;
                spanCount.setAttribute('style','background-color: #f50909 !important;');
                allChatMessages.appendChild(spanCount);
              }

              var totalMsgCount = document.getElementById('msg_count_1_'+data.receiver).innerHTML;
              if(totalMsgCount == ''){
                document.getElementById('msg_count_1_'+data.receiver).innerHTML = 1;
              } else {
                document.getElementById('msg_count_1_'+data.receiver).innerHTML = parseInt(totalMsgCount)+1;;
              }

              var allMsgCount = document.getElementById('msg_count_2_'+data.receiver).innerHTML;
              if(allMsgCount == ''){
                document.getElementById('msg_count_2_'+data.receiver).innerHTML = 1;
              } else {
                document.getElementById('msg_count_2_'+data.receiver).innerHTML = parseInt(allMsgCount)+1;;
              }

              var currentUserImage = document.getElementById('currentUserImage');
              if( null == document.getElementById('userCnt_'+data.receiver)){
                var bCount = document.createElement('b');
                if(current_user == data.sender){
                  bCount.className = "hide";
                }
                bCount.id = 'userCnt_'+data.receiver;
                bCount.innerHTML = 0;
                bCount.setAttribute('style','color: red !important;');
                currentUserImage.appendChild(bCount);
              }

              if(parseInt(document.getElementById('userCnt_'+data.receiver).innerHTML) > 0){
                document.getElementById('userCnt_'+data.receiver).innerHTML = parseInt(document.getElementById('userCnt_'+data.receiver).innerHTML) + 1;
              } else {
                document.getElementById('userCnt_'+data.receiver).innerHTML = 1;
              }
              var textMessage = data.message.replace(/<[^>]+>/ig, '');
              if(current_user ==  data.sender && userchat.length > 0){
                  var liEle = document.createElement('li');
                  liEle.className = 'right clearfix addChat';
                  liEle.innerHTML = '<span class="chat-img pull-right "><img src="'+data.senderImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+textMessage+'</p></div><div class="chat-time clearfix"><span class="pull-right">'+messagteTime(data.created_at)+'</span></div>';
                  userchat[0].appendChild(liEle);
              } else if(userchat.length > 0){
                  var liEle = document.createElement('li');
                  liEle.className = 'left clearfix addChat';
                  liEle.innerHTML = '<span class="chat-img pull-left "><img src="'+data.senderImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+textMessage+'</p></div><div class="chat-time clearfix"><span class="pull-left">'+messagteTime(data.created_at)+'</span></div>';
                  userchat[0].appendChild(liEle);
              }
              if(userchat.length > 0){
                $(userchat).parent().animate({scrollTop:$(userchat)[0].scrollHeight});
              }
          });
      };
      // send & insert message
      function sendMessage(ele) {
          var id = $(ele).data('send_id');
          message = document.getElementById('message_'+id).value;
          if(message != ''){
              var roomArr = [];
              roomArr.push(id);
              roomArr.push(document.getElementById('user_id').value);
              var roomMembers = roomArr.sort();
              var room = 'private_'+roomArr[0]+'_'+roomArr[1];
              var user = document.getElementById('currentUserName').value;
              var chatroomId = $(ele).data('chatroom_id');
              var receiver = id;
              var sender = document.getElementById('user_id').value;
              var token = "{{ csrf_token() }}";
              var created_at = new Date();
              var senderImgPath = $('#currentUserImage').attr('src');
              socket.emit('send', { room: room, message: message, user:user, sender:parseInt(sender) ,receiver:receiver, created_at:created_at,senderImgPath:senderImgPath});
              $.ajax({
                  type: "POST",
                  url: '{!! URL::to("sendMessage") !!}',
                  dataType: "json",
                  data: {'_token':token,'message':message,'sender':parseInt(sender) ,'receiver':receiver, 'chatroomId':chatroomId},
                  success:function(data){
                      document.getElementById('message_'+id).value = '';
                  }
              });
          }else{
              alert("Please Add Message.");
          }
      }

      $(window).on('keydown', function(e) {
        var receiverId = $(e.target).attr('data-receiver_id');
        if (e.which == 13 && receiverId > 0) {
          $('#send_'+receiverId).click();
          return false;
        }
      });
  </script>
@endif