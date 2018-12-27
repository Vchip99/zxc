<nav class="navbar navbar-fixed-top" style="background: black;">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="https://vchipedu.com" style="color: white;">Vchip Edu</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav navbar-right">
        <li class="active"><a href="/" >HOME</a></li>
        <li><a href="{{ url('mentors') }}">Mentors</a></li>
        <li><a href="{{ url('faq') }}" >FAQ</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-user-circle-o" style="font-size:24px;"></i><span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            @if(is_object(Auth::user()))
              <li><a href="{{ url('messages') }}">Messages</a></li>
              <li><a href="{{ url('schedules') }}">Schedules</a></li>
              <li><a href="{{ url('logout') }}" data-toggle="tooltip" title="Logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                  <form id="logout-form" action="{{ url('userLogout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                  </form>
                </li>
                <input type="hidden" name="user_id" id="user_id" value="{{Auth::user()->id}}">
            @else
              <li><a onClick="checkLogin()">Sign-in</a></li>
              <li><a href="{{ url('mentee/signup')}}">Sign-up</a></li>
            @endif
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>