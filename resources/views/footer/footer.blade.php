<footer>
 <div class="footer" >
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3> Services</h3>
          <ul>
            <li class=""><a href="{{ url('erp') }}">Digital Edu & ERP</a></li>
            <li class=""><a href="{{ url('educationalPlatform') }}">Education Platform</a></li>
            <li class=""><a href="{{ url('digitalMarketing') }}">Digital Marketing</a></li>
            <li class=""><a href="{{ url('pricing') }}">Pricing</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3 class="hidden-2"> Digital Education</h3>
          <h3 class="hidden-1">Education</h3>
          <ul >
             <li><a href="{{ url('courses')}}">Online Courses</a></li>
             <li><a href="{{ url('liveCourse') }}">Live Course</a></li>
             <li class=""></li>
             <li><a href="{{ url('online-tests') }}">Online Test Series</a></li>
             <li class=""></li>
             <li><a href="{{ url('workshops') }}">Workshops</a></li>
             <li class=""></li>
             <li><a href="{{ url('vkits') }}">Hobby Projects</a></li>
             <li class=""></li>
             <li><a href="{{ url('documents') }}">Documents</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3 class="hidden-2">Placement & Other</h3>
          <h3 class="hidden-1">Placement</h3>
          <ul>
            <li><a href="{{ url('placements')}}">Placement</a></li>
            <li><a href="{{ url('/showTest') }}/1">Placement Mock Test</a></li>
            <li><a href="{{url('discussion')}}">Discussion Forum</a></li>
            <li><a href="{{url('blog')}}">Blogs</a></li>
            <li><a href="{{url('ourpartner')}}">Our Partners</a></li>
            <li><a href="{{url('career')}}">Career</a></li>
            <li><a href="{{url('admin/login')}}">Admin Dashboard</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3> Contact Us </h3>
          <address>
           <p>VCHIP TECHNOLOGY PVT LTD</p>
           <p>Address: GITANJALI COLONY, NEAR RAJYOG SOCIETY, </p>
           <p> WARJE, PUNE-411058, INDIA.</p>
           <p>Email: info@vchiptech.com</p>
           <form action="{{url('subscribedUser')}}" method="POST">
            {{csrf_field()}}
              <div class="v_subscribe_form input-group">
                 <input class="btn btn-sm" name="email" id="email" type="email" placeholder="Email" required>
                 <button class=" btn-info btn-sm" type="submit">Subscribe</button>
              </div>
           </form>
         </address>
        </div>
      </div>
   </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <div class="row">
        <div class="col-lg-4  col-md-4 col-sm-6 col-xs-6">
          <p class="pull-left" title="vchiptech.com"><a href="https://vchiptech.com/" class="site_link" target="_blank"> vchiptech.com </a></p>
        </div>
        <div class="col-lg-4  col-md-4 col-sm-6 col-xs-6 text-center social-contact" >
          <ul class="social-network social-circle ">
            <li><a href="https://www.facebook.com/vchip99/" class="icoFacebook" title="Facebook"><i class="fa fa-facebook"></i></a></li>
            <li><a href="#" class="icoTwitter" title="Twitter"><i class="fa fa-twitter"></i></a></li>
            <li><a href="https://plus.google.com/u/0/115493121296973872760" class="icoGoogle" title="Google +"><i class="fa fa-google-plus"></i></a></li>
            <li><a href="https://www.linkedin.com/company/13213434/" class="icoLinkedin" title="Linkedin"><i class="fa fa-linkedin"></i></a></li>
          </ul>
        </div>
        <div class="col-lg-4  col-md-4 col-sm-6 col-xs-12 ">
          <p class="pull-right" title="vchipedu.com"><a href="https://vchipedu.com/" class="site_link" target="_blank"> vchipedu.com </a></p>
        </div>
      </div>
    </div>
  </div>
  @if(Auth::user())
  <input type="hidden" id="currentUser" value="{{ Auth::user()->id }}">
  <input type="hidden" id="currentUserName" value="{{ Auth::user()->name }}">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="row chat-window " id="chat_window_1" style="margin-left:10px;">
                    <div class="col-xs-12 col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading top-bar panel-collapsed">
                              <div class="pull-left">
                                <h3 class="panel-title"><span class="fa fa-comments"></span> <b>Messaging</b>
                                  <span id="message_header">
                                    <span class="badge" style="background-color: #f50909 !important;" id="msg_count_1_{{ Auth::user()->id }}"></span>
                                  </span>
                                  </span>
                                </h3>
                              </div>
                              <div class="pull-right" >
                                <a ><span id="minim_chat_window" class="fa fa-minus icon_minim"></span></a>
                              </div>
                            </div>
                            <div class="panel-body" style="display: none;">
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
  @endif
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
              <input id="useremail" name="email" type="text" class="form-control" placeholder="vchip@gmail.com" onfocus="changeType('email');" autocomplete="off" required>
              <span class="help-block"></span>
            </div>
            <div class="form-group">
              <input id="password" name="password" type="text" class="form-control" placeholder="password" data-type="password" onfocus="changeType('password');" autocomplete="off" required >
              <span class="help-block"></span>
            </div>
            <div id="loginErrorMsg" class="hide">Wrong username or password</div>
            <button type="submit" value="login" name="submit" class="btn btn-info btn-block" onClick="loginUser();">Login</button>
            <br />
            <div class="form-group">
              <a href="{{ url('/auth/facebook') }}" class="btn btn-facebook btn-info" style="width: 209px; background-color: #3B5998; border-color: #3B5998;"><i class="fa fa-facebook"></i> Login </a>
            </div>
            <div class="form-group">
              <a href="{{ url('/auth/google') }}" class="btn btn-google btn-info" style="width: 209px; background-color: #DD4B39; border-color: #DD4B39;"><i class="fa fa-google"></i> Login </a>
            </div>
            <div class="form-group">
              <div class="col-md-12 control">
                  <div style="margin-top: 10px; margin-bottom: 20px;  color:#fff;" >
                      Need an account?
                  <a href="{{ url('signup')}}" ">Sign Up</a>
                  </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  function loginUser(){
    var email = document.getElementById('useremail').value;
    var password = document.getElementById('password').value;
    if(email && password){
      $.ajax({
          method: "POST",
          url: "{{ url('userLogin') }}",
          data: {email:email, password:password}
      })
      .done(function( msg ) {
        if('true' == msg){
          window.location.reload(true);
        } else {
          document.getElementById('loginErrorMsg').classList.remove('hide');
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
  });
</script>
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
        var right = 320;
        var iii = 0;
        for(iii; iii < total_popups; iii++)
        {
            if(popups[iii] != undefined)
            {
                var element = document.getElementById(popups[iii]);
                element.style.right = right + "px";
                right = right + 320;
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
        if(width < 540)
        {
            total_popups = 0;
        }
        else
        {
            width = width - 200;
            //320 is width of a single popup box
            total_popups = parseInt(width/320);
        }
        display_popups();
    }
    //recalculate when window is loaded and also when window is resized.
    window.addEventListener("resize", calculate_popups);
    window.addEventListener("load", calculate_popups);
</script>
<script type="text/javascript">
    var socket = io.connect('http://'+window.location.host+':8890');

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
        // add chat window
        divChatWindows = document.getElementById('userchatwindow');

        var popupBoxDiv = document.createElement('div');
        popupBoxDiv.className = 'popup-box chat-popup';
        popupBoxDiv.id = 'qnimate_'+receiverId;

        var popupHeadDiv = document.createElement('div');
        popupHeadDiv.className = 'popup-head';
        popupHeadDiv.innerHTML = '<div class=" pull-left"><b>'+receiverName+'</b></div><div class="popup-head-right pull-right"><a data-widget="remove" id="'+receiverId+'" class="chat-header-button pull-right" type="button" onclick="closeChat(this);"><i class="fa fa-remove"></i></a></div>';
        popupBoxDiv.appendChild(popupHeadDiv);

        var popupMessageDiv = document.createElement('div');
        popupMessageDiv.className = 'popup-messages';
        popupMessageDiv.id = receiverId;
        popupMessageDiv.innerHTML = '<ul class="chat userchat" id="'+'chatmessages_'+roomArr[0]+'_'+roomArr[1]+'"></ul>';
        popupBoxDiv.appendChild(popupMessageDiv);

        var popupMessageFooterDiv = document.createElement('div');
        popupMessageFooterDiv.className = 'popup-messages-footer';
        popupMessageFooterDiv.innerHTML = '<textarea id="message_'+receiverId+'"  data-receiver_id="'+receiverId+'" placeholder="Type a message..." rows="10" cols="40" name="message" onfocus="readmessagecount(this);"></textarea><div class="btn-footer"><button class="pull-right send-msg" id="send_'+receiverId+'" data-send_id="'+receiverId+'" data-chatroom_id=""  onclick="sendMessage(this);"><span class="fa fa-share"></span> Share</button><input type="hidden" id="message_limit_'+receiverId+'" value="0"><input type="hidden" id="is_scroll_'+receiverId+'" value="1"></div>';
        popupBoxDiv.appendChild(popupMessageFooterDiv);
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
                $.each(messages['messages'],function(idx,obj){
                  if(current_user == obj.sender_id){
                      $('#chatmessages_'+roomArr[0]+'_'+roomArr[1]).prepend('<li class="right clearfix addChat"><span class="chat-img pull-right "><img src="'+senderImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+obj.message+'</p></div><div class="chat-time clearfix"><span class="pull-right">'+messagteTime(obj.created_at)+'</span></div></li>');
                  } else {
                      $('#chatmessages_'+roomArr[0]+'_'+roomArr[1]).prepend('<li class="left clearfix addChat"><span class="chat-img pull-left "><img src="'+receiverImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+obj.message+'</p></div><div class="chat-time clearfix"><span class="pull-left">'+messagteTime(obj.created_at)+'</span></div></li>');
                  }
                });
                $(chatMessageId).parent().animate({scrollTop:$(chatMessageId)[0].scrollHeight});
                if(messages['chatroom_id']){
                    document.getElementById('send_'+receiverId).setAttribute('data-chatroom_id',messages['chatroom_id']);
                }
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

    // make count zero
    function readmessagecount(ele){
      var receiver = $(ele).attr('data-receiver_id');
      var current_user = document.getElementById('user_id').value;
      var token = "{{ csrf_token() }}";
      if(parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML) > 0 && parseInt(document.getElementById('unread_'+receiver).innerHTML) > 0){
        document.getElementById('msg_count_1_'+current_user).innerHTML -= parseInt(document.getElementById('unread_'+receiver).innerHTML);
        document.getElementById('msg_count_2_'+current_user).innerHTML = parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML);
        if(document.getElementById('userCnt_'+current_user).innerHTML > 0)  {
          document.getElementById('userCnt_'+current_user).innerHTML = parseInt(document.getElementById('userCnt_'+current_user).innerHTML) - parseInt(document.getElementById('unread_'+receiver).innerHTML);
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
      document.getElementById('unread_'+receiver).innerHTML = '';
    }

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
                          var userImagePath = obj['photo'];
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
                        spanUnread.id = 'unread_'+obj['id'];
                        spanUnread.setAttribute('style','color: red;');
                        if(users['unreadCount'][obj.id] > 0){
                          spanUnread.innerHTML = ' ' + users['unreadCount'][obj.id];
                          if(parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML) > 0){
                            document.getElementById('msg_count_1_'+current_user).innerHTML = parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML)+parseInt(users['unreadCount'][obj.id]);
                          } else {
                            document.getElementById('msg_count_1_'+current_user).innerHTML = 0+parseInt(users['unreadCount'][obj.id]);
                          }
                            document.getElementById('msg_count_2_'+current_user).innerHTML = parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML);
                            if(document.getElementById('userCnt_'+current_user).innerHTML > 0)  {
                              document.getElementById('userCnt_'+current_user).innerHTML = parseInt(document.getElementById('userCnt_'+current_user).innerHTML) + parseInt(users['unreadCount'][obj.id]);
                            } else {
                              document.getElementById('userCnt_'+current_user).innerHTML = 0 + parseInt(users['unreadCount'][obj.id]);
                            }
                        }
                        divHeader.appendChild(spanUnread);

                        var spanStatus = document.createElement('span');
                        spanStatus.className = 'chat-img pull-right';
                        if(obj['is_online']){
                            spanStatus.innerHTML += '<img src="/images/online.png" id="userstatus_'+obj['id']+'" data-user_id="'+obj['id']+'" style="height:  20px; width: 20px;" />';

                        } else {
                            spanStatus.innerHTML += '<img src="/images/offline.png" id="userstatus_'+obj['id']+'" data-user_id="'+obj['id']+'" style="height:  20px; width: 20px;" />';
                        }
                        divHeader.appendChild(spanStatus);
                        divChatBody.appendChild(divHeader);

                        var pEle = document.createElement('p');
                        pEle.innerHTML = 'Vchip Technology';
                        divChatBody.appendChild(pEle);
                        liEle.appendChild(divChatBody);
                        chatUsers.appendChild(liEle);
                    });
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
              var current_user = document.getElementById('user_id').value;
                var chatUsers = document.getElementById('chat_users');
                document.getElementById('previuos_chat_users').value = users['chatusers']['chat_users'];
                $.each(users['chatusers']['users'],function(idx,obj){
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
                      var userImagePath = obj['photo'];
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
                    spanUnread.id = 'unread_'+obj['id'];
                    spanUnread.setAttribute('style','color: red;');
                    if(users['unreadCount'] && users['unreadCount'][obj.id] > 0){
                      spanUnread.innerHTML = ' ' + users['unreadCount'][obj.id];
                      if(parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML) > 0){
                        document.getElementById('msg_count_1_'+current_user).innerHTML = parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML)+parseInt(users['unreadCount'][obj.id]);
                      } else {
                        document.getElementById('msg_count_1_'+current_user).innerHTML = 0+parseInt(users['unreadCount'][obj.id]);
                      }
                      document.getElementById('msg_count_2_'+current_user).innerHTML = parseInt(document.getElementById('msg_count_1_'+current_user).innerHTML);
                      if(document.getElementById('userCnt_'+current_user).innerHTML > 0)  {
                        document.getElementById('userCnt_'+current_user).innerHTML = parseInt(document.getElementById('userCnt_'+current_user).innerHTML) + parseInt(users['unreadCount'][obj.id]);
                      } else {
                        document.getElementById('userCnt_'+current_user).innerHTML = 0 + parseInt(users['unreadCount'][obj.id]);
                      }
                    }
                    divHeader.appendChild(spanUnread);

                    var spanStatus = document.createElement('span');
                    spanStatus.className = 'chat-img pull-right';
                    if(obj['is_online']){
                        spanStatus.innerHTML += '<img src="/images/online.png" id="userstatus_'+obj['id']+'" data-user_id="'+obj['id']+'" style="height:  20px; width: 20px;" />';

                    } else {
                        spanStatus.innerHTML += '<img src="/images/offline.png" id="userstatus_'+obj['id']+'" data-user_id="'+obj['id']+'" style="height:  20px; width: 20px;" />';
                    }
                    divHeader.appendChild(spanStatus);
                    divChatBody.appendChild(divHeader);

                    var pEle = document.createElement('p');
                    pEle.innerHTML = 'Vchip Technology';
                    divChatBody.appendChild(pEle);
                    liEle.appendChild(divChatBody);
                    chatUsers.appendChild(liEle);
                });
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

    function closeChat(ele){
        var id = $(ele).attr('id');
        close_popup('qnimate_'+id);
    }
    // up & down chat users list
    $(document).on('click', '.top-bar', function (e) {
      var $this = $(this);
      if(!$this.hasClass('panel-collapsed')) {
        $this.parents('.panel').find('.panel-body').slideUp();
        $this.addClass('panel-collapsed');
        $('#minim_chat_window').removeClass('fa-minus').addClass('fa-plus');
      } else {
        $this.parents('.panel').find('.panel-body').slideDown();
        $this.removeClass('panel-collapsed');
        $('#minim_chat_window').removeClass('fa-plus').addClass('fa-minus');
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
                            if(current_user == obj.sender_id){
                                $('#chatmessages_'+roomArr[0]+'_'+roomArr[1]).prepend('<li class="right clearfix addChat"><span class="chat-img pull-right "><img src="'+senderImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+obj.message+'</p></div><div class="chat-time clearfix"><span class="pull-right">'+messagteTime(obj.created_at)+'</span></div></li>');
                            } else {
                                $('#chatmessages_'+roomArr[0]+'_'+roomArr[1]).prepend('<li class="left clearfix addChat"><span class="chat-img pull-left "><img src="'+receiverImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+obj.message+'</p></div><div class="chat-time clearfix"><span class="pull-left">'+messagteTime(obj.created_at)+'</span></div></li>');
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
                if(onlineusers.length > 0){
                    var messageUsers = $('img[id^=userstatus_]');
                    $.each(messageUsers, function(idx, obj){
                        var userId = $(obj).data('user_id');
                        if(current_user != userId){
                            var userStatus = document.getElementById('userstatus_'+userId);
                            if(onlineusers.indexOf(userId) > -1){
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
            if( null == document.getElementById('unread_'+data.sender)){
              var spanUnread = document.createElement('span');
              spanUnread.className = "hide";
              spanUnread.id = 'unread_'+data.sender;
              spanUnread.setAttribute('style','color: red;');
              spanUnread.value = 1;
              userchat[0].appendChild(spanUnread);
            }
            var unread = document.getElementById('unread_'+data.sender).innerHTML;
            if(unread == ''){
              document.getElementById('unread_'+data.sender).innerHTML = 1;
            } else {
              document.getElementById('unread_'+data.sender).innerHTML = parseInt(unread)+1;;
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
              bCount.setAttribute('style','color: red !important;');
              currentUserImage.appendChild(bCount);
            }

            document.getElementById('userCnt_'+data.receiver).innerHTML = parseInt(document.getElementById('userCnt_'+data.receiver).innerHTML) + 1;

            if(current_user ==  data.sender){
                var liEle = document.createElement('li');
                liEle.className = 'right clearfix addChat';
                liEle.innerHTML = '<span class="chat-img pull-right "><img src="'+data.senderImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+data.message+'</p></div><div class="chat-time clearfix"><span class="pull-right">'+messagteTime(data.created_at)+'</span></div>';
                userchat[0].appendChild(liEle);
            } else {
                var liEle = document.createElement('li');
                liEle.className = 'left clearfix addChat';
                liEle.innerHTML = '<span class="chat-img pull-left "><img src="'+data.receiverImgPath+'" alt="User Avatar" class="img-circle" /></span><div class="chat-body clearfix"><p>'+data.message+'</p></div><div class="chat-time clearfix"><span class="pull-left">'+messagteTime(data.created_at)+'</span></div>';
                userchat[0].appendChild(liEle);
            }
            $(userchat).parent().animate({scrollTop:$(userchat)[0].scrollHeight});
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
            var receiverImgPath = $($('li#'+id+' span img')[0]).attr('src');
            socket.emit('send', { room: room, message: message, user:user, sender:parseInt(sender) ,receiver:receiver, created_at:created_at,senderImgPath:senderImgPath,receiverImgPath:receiverImgPath });
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
</script>