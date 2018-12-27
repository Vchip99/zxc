<nav class="navbar navbar-fixed-top" style="background: black;">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="https://vchipedu.com">Vchip Edu</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav navbar-right">
        <li class="active"><a href="/">HOME</a></li>
        <li><a href="{{ url('mentors') }}">Mentors</a></li>
        <li><a href="{{ url('faq') }}" >FAQ</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-user-circle-o" style="font-size:24px;"></i><span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Messages</a></li>
            <li><a href="#">Schedules</a></li>
            <li><a href="#">Sign-in</a></li>
            <li><a href="#">Sign-up</a></li>
            <li><a href="#">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>