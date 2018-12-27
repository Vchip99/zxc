<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <link rel="SHORTCUT ICON" href="{{ asset('images/logo/vedu.png') }}"/>
  <title>Dashboard</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link href="{{ asset('css/bootstrap.min.css?ver=1.0')}}" rel="stylesheet">
  <link href="{{ asset('css/font-awesome/css/font-awesome.min.css?ver=1.0')}}" rel="stylesheet"/>

  <link href="{{ asset('css/sidemenu/sidemenu_layout.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/sidemenu/_all-skins.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/v_main.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/animate.min.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/comment.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/jquery-confirm.min.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/nav_footer.css?ver=1.0')}}" rel="stylesheet"/>

  <script src="{{ asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  <script src="{{ asset('js/jquery.min.js?ver=1.0')}}"></script>
  <script src="{{ asset('js/bootstrap.min.js?ver=1.0')}}"></script>
  <script src="{{ asset('js/app.js') }}"></script>
  <script src="{{ asset('js/jquery-confirm.min.js?ver=1.0')}}"></script>
  <script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
  </script>
  <style type="text/css">
  .admin_table{
    padding-top: 10px;
    background-color: #01bafd;
  }
  .admin_div{
    padding: 10px;
    background-color: #01bafd;
  }
  .btn-primary {
    background-color: #3c8dbc;
    border-color: #367fa9;
  }
  </style>
  @yield('dashboard_header')
</head>
<body class="hold-transition skin-blue sidebar-mini sidebar-collapse">
<div class="wrapper">
  <header class="main-header">
    <a href="{{ url('mentor/profile')}}" class="logo">
      <span class="logo-mini"><b></b></span>
      <span class="logo-lg"><b>Mentor</b></span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
        </ul>
      </div>
    </nav>
  </header>
  <aside class="main-sidebar">
    <section class="sidebar">
      <div class="user-panel">
        <div class="pull-left image">
          <a href="{{ url('mentor/profile')}}">
          @if(is_file(Auth::guard('mentor')->user()->photo) || (!empty(Auth::guard('mentor')->user()->photo) && false == preg_match('/mentorImages/',Auth::guard('mentor')->user()->photo)))
            <img src="{{ asset(Auth::guard('mentor')->user()->photo)}}" id="dashboardUserImage" class="img-circle" alt="User Image">
          @else
            <img src="{{ url('images/user/user1.png')}}" id="dashboardUserImage" class="img-circle" alt="User Image">
          @endif
          </a>
        </div>
        <div class="pull-left info">
          <p><a href="{{ url('profile')}}">{{ ucfirst(Auth::guard('mentor')->user()->name)}}</a></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          <input type="hidden" name="user_id" id="user_id" value="{{ (is_object(Auth::guard('mentor')->user()))?Auth::guard('mentor')->user()->id: NULL }}"/>
        </div>
      </div>
      <ul class="sidebar-menu">
        <li class="treeview">
          <a href="#" title="Profile">
            <i class="fa fa-user"></i> <span>Profile</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Mentor Profile"><a href="{{ url('mentor/profile')}}"><i class="fa fa-circle-o"></i>Mentor Profile</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#" title="Schedule">
            <i class="fa fa-calendar"></i> <span>Schedule</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Calendar"><a href="{{ url('mentor/calendar')}}"><i class="fa fa-circle-o"></i>Calendar</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#" title="Messages">
            <i class="fa fa-comments"></i> <span>Messages</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Messages"><a href="{{ url('mentor/messages')}}"><i class="fa fa-circle-o"></i>Messages</a></li>
          </ul>
        </li>
        <li class="header">LABELS</li>
        <li>
          <a href="{{ url('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-sign-out" aria-hidden="true"></i> <span>Logout </span>
            <span class="pull-right-container"></span>
          </a>
          <form id="logout-form" action="{{ url('mentor/logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
          </form>
        </li>
      </ul>
    </section>
  </aside>
  <div class="content-wrapper">
    @yield('module_title')
    <section class="v-container">
      <div class="container">
        <div class="row">
          <div class="col-sm-9">
            @yield('dashboard_content')
          </div>
        </div>
      </div>
    </section>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
        setTimeout(function() {
          $('.alert-success').fadeOut('fast');
        }, 10000); // <-- time in milliseconds
    });

</script>

</body>
</html>
