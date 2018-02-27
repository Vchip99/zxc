<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Dashboard</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link href="{{ asset('css/bootstrap.min.css?ver=1.0')}}" rel="stylesheet">
  <link href="{{ asset('css/font-awesome/css/font-awesome.min.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/sidemenu/sidemenu_layout.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/sidemenu/_all-skins.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/v_main.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/comment.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/jquery-confirm.min.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css?family=Hind" rel="stylesheet">

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
    <a href="{{ url('/')}}" class="logo">
      <span class="logo-mini"><b> V</b>EDU</span>
      <span class="logo-lg"><b>Vchip</b>Technology</span>
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
          <a href="{{ url('profile')}}">
          @if(!empty(Auth::guard('clientuser')->user()->photo))
            <img src="{{ asset(Auth::guard('clientuser')->user()->photo)}}" class="img-circle" alt="User Image" >
          @else
            <img src="{{ asset('images/user1.png')}}" class="img-circle" alt="User Image">
          @endif
          </a>
        </div>
        <div class="pull-left info">
          <p><a href="{{ url('profile')}}">{{ ucfirst(Auth::guard('clientuser')->user()->name)}}</a></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          <input type="hidden" name="user_id" id="user_id" value="{{ (is_object(Auth::guard('clientuser')->user()))?Auth::guard('clientuser')->user()->id: NULL }}"/>
        </div>
      </div>
      <ul class="sidebar-menu">
        <li class="header">Vchip Technology</li>
        <li class="treeview ">
          <a href="#">
            <i class="fa fa-star"></i><span>Notifications </span><b style="color: red;">{{Auth::guard('clientuser')->user()->userNotificationCount()}}</b>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('myNotifications')}}"><i class="fa fa-circle-o"></i> My Notifications : <b style="color: red;">{{Auth::guard('clientuser')->user()->userNotificationCount()}} </b></a></li>
            <li><a href="{{ url('clientMessages')}}"><i class="fa fa-circle-o"></i> Admin Messages : <b style="color: red;">{{Auth::guard('clientuser')->user()->adminNotificationCount()}} </b></a></li>
          </ul>
        </li>
        <li class="treeview ">
          <a href="#" title="Online Courses">
            <i class="fa fa-dashboard"></i> <span>Online Courses</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="My Online Courses"><a href="{{ url('myCourses')}}"><i class="fa fa-circle-o"></i> My Online Courses</a></li>
            <li title="My Course Results"><a href="{{ url('myCourseResults')}}"><i class="fa fa-circle-o"></i> My Course Results</a></li>
            <li title="My Certificate"><a href="{{ url('myCertificate')}}"><i class="fa fa-circle-o"></i> My Certificate</a></li>
            <li title="More Courses"><a href="{{ url('online-courses')}}"><i class="fa fa-circle-o"></i> More Courses</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#" title="Online Test">
            <i class="fa fa-files-o"></i>
            <span>Online Test</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="My Test"><a href="{{ url('myTest')}}"><i class="fa fa-circle-o"></i> My Test</a></li>
            <li title="My Test Results"><a href="{{ url('myTestResults')}}"><i class="fa fa-circle-o"></i> My Test Results</a></li>
            <li title="More Test"><a href="{{ url('online-tests')}}"><i class="fa fa-circle-o"></i> More Test</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-tasks"></i> <span>Assignment</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('myAssignments')}}"><i class="fa fa-circle-o"></i> My Assignments</a></li>
          </ul>
        </li>
        <!-- <li title="Home"><a href="{{ url('profile')}}"><i class="fa fa-user"></i> <span>Profile</span></a></li> -->
        <!-- <li title="Home"><a href="{{ url('/')}}"><i class="fa fa-home"></i> <span>Home</span></a></li> -->
        <li class="header">LABELS</li>
        <li title="Logout">
          <a href="{{ url('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-sign-out" aria-hidden="true"></i> <span>Logout </span>
            <span class="pull-right-container"></span>
          </a>
          <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
          </form>
        </li>
      </ul>
    </section>
  </aside>
  <div class="content-wrapper">
    @yield('module_title')
    <section class="content">
      <div class="row">
        <div class="col-sm-12">
          @yield('dashboard_content')
        </div>
      </div>
    </section>
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
