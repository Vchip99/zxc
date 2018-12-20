<nav class=" navbar-lower navbar navbar-default  shrink navbar-fixed-top" style=" z-index: 1030; ">
  <div class="container">
    <div class="pull-left" >
      <a class="navbar-brand pull-left" href="{{ url('/')}}">
        <span>Vchip-edu</span>
      </a>
    </div>
    <div class="navbar-header pull-right">
      <button type="button" class="navbar-toggle pull-left" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <div class="pull-right dropdown " >
        @if(Auth::user())
          <a href="#" class="dropdown-toggle pull-right user_menu" data-toggle="dropdown" role="button" aria-expanded="false" title="User">
            @if(is_file(Auth::user()->photo) || (!empty(Auth::user()->photo) && false == preg_match('/userStorage/',Auth::user()->photo)))
              <img src="{{asset(Auth::user()->photo)}}" id="currentUserImage" class="img-circle user-profile" alt="user name" aria-haspopup="true"   aria-expanded="true"/>&nbsp;
            @else
              <img src="{{ asset('images/user1.png') }}" id="currentUserImage" class="img-circle user-profile" alt="user name" aria-haspopup="true"   aria-expanded="true"/>&nbsp;
            @endif
            @if(Auth::user()->userNotificationCount() > 0)
              <b style="color: red;" id="userCnt_{{Auth::user()->id}}">{{Auth::user()->userNotificationCount()}}</b>
            @else
              <b style="color: red;" id="userCnt_{{Auth::user()->id}}"></b>
            @endif
          </a>
          <ul class="dropdown-menu user-dropdown ">
            <li>
              <a href="{{ url('college/'.Session::get('college_user_url').'/profile')}}" target="_blank" data-toggle="tooltip" title="DASHBOARD">
              @if(is_file(Auth::user()->photo) || (!empty(Auth::user()->photo) && false == preg_match('/userStorage/',Auth::user()->photo)))
                <img src="{{asset(Auth::user()->photo)}}" class="img-circle user-profile1" alt="user name" aria-haspopup="true"   aria-expanded="true"/>&nbsp;
              @else
                <img src="{{ asset('images/user1.png') }}" class="img-circle user-profile1" alt="user name" aria-haspopup="true"   aria-expanded="true"/>&nbsp;
              @endif
              DASHBOARD
              </a>
            </li>
            <li role="separator" class="divider"></li>
            <li>
              <a href="{{ url('college/'.Session::get('college_user_url').'/allChatMessages')}}" target="_blank" data-toggle="tooltip" title="Chat Messages"><i class="fa fa-star" aria-hidden="true"></i> Chat Messages : <b style="color: red;" id="all_chat_messages"><span id="msg_count_2_{{Auth::user()->id}}">0</span></b></a>
            </li>
            <li>
              <a href="{{ url('college/'.Session::get('college_user_url').'/myNotifications')}}" target="_blank" data-toggle="tooltip" title="My Notifications"><i class="fa fa-star" aria-hidden="true"></i> My Notifications : <b style="color: red;">{{Auth::user()->userNotificationCount()}}</b></a>
            </li>
            <li>
              <a href="{{ url('college/'.Session::get('college_user_url').'/adminMessages')}}" target="_blank" data-toggle="tooltip" title="Admin Notifications"><i class="fa fa-star" aria-hidden="true"></i> Admin Messages : <b style="color: red;">{{Auth::user()->adminNotificationCount()}}</b></a>
            </li>
            <li role="separator" class="divider"></li>
            <li><a href="{{ url('logout') }}" data-toggle="tooltip" title="Logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
              <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
              </form>
            </li>
          </ul>
        @else
          <a href="#" class="dropdown-toggle pull-right user_menu" data-toggle="dropdown" role="button" aria-expanded="false" title="User"><img src="{{ asset('images/user1.png') }}" class="img-circle user-profile" alt="user name" aria-haspopup="true" aria-expanded="true"/>
          </a>
          <ul class="dropdown-menu" role="menu">
            <div class="navbar-content">
              <li>
                <a onClick="checkLogin();" style="cursor: pointer;"> Login</a>
              </li>
              <li>
                <a href="{{ url('signup')}}"> SignUp</a>
              </li>
            </div>
          </ul>
        @endif
      </div>
    </div>
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        @if(count($categories) > 0)
          @foreach($categories as $categoryId => $category)
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="Digital Education">{{$category}} <span class="caret"></span>
              </a>
              <ul class="dropdown-menu" role="menu">
                <div class="navbar-content">
                @if(count($subcategories[$categoryId] > 0))
                  @foreach($subcategories[$categoryId] as $subcategoryId => $subcategoryArr)
                    <li><a href="{{url('study-material')}}/{{$subcategoryId}}/{{$subcategoryArr['subject']}}/{{$subcategoryArr['topic_id']}}"> {{$subcategoryArr['name']}}</a></li>
                    <li class="divider"></li>
                  @endforeach
                @endif
                </div>
              </ul>
            </li>
          @endforeach
        @endif
      </ul>
    </div>
  </div>
</nav>
<script type="text/javascript">
  $(function () {
  $('.navbar-collapse ul li a:not(.dropdown-toggle)').click(function () {
    $('.navbar-toggle:visible').click();
  });
});
if ($(window).width() > 1201) {
  $('.dropdown').hover(function() {
    $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(500);
  }, function() {
    $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(500);
  });
}
</script>