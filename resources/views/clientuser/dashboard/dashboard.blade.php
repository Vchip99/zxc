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
    @if(!Session::has('parent_'.Auth::guard('clientuser')->user()->parent_phone))
    <a href="{{ url('/')}}" class="logo">
      <span class="logo-mini"><b> Home</b></span>
      <span class="logo-lg"><b>Home</b></span>
    </a>
    @endif
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
          @if(!empty(Auth::guard('clientuser')->user()->photo) && is_file(Auth::guard('clientuser')->user()->photo))
            <img src="{{ asset(Auth::guard('clientuser')->user()->photo)}}" class="img-circle" alt="User Image" >
          @else
            <img src="{{ asset('images/user1.png')}}" class="img-circle" alt="User Image">
          @endif
          </a>
        </div>
        <div class="pull-left info">
          <p>
            <a href="{{ url('profile')}}">
            @if(Session::has('parent_'.Auth::guard('clientuser')->user()->parent_phone))
              {{ ucfirst(Auth::guard('clientuser')->user()->parent_name)}}
            @else
              {{ ucfirst(Auth::guard('clientuser')->user()->name)}}
            @endif
            </a>
          </p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          <input type="hidden" name="user_id" id="user_id" value="{{ (is_object(Auth::guard('clientuser')->user()))?Auth::guard('clientuser')->user()->id: NULL }}"/>
          <input type="hidden" name="client_id" id="client_id" value="{{ (is_object(Auth::guard('clientuser')->user()))?Auth::guard('clientuser')->user()->client_id: NULL }}"/>
        </div>
      </div>
      <ul class="sidebar-menu">
        <li class="header">Dashboard</li>
        @if(!Session::has('parent_'.Auth::guard('clientuser')->user()->parent_phone))
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
        @endif
        <li class="treeview ">
          <a href="#" title="Online Courses">
            <i class="fa fa-dashboard"></i> <span>Online Courses</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @if(!Session::has('parent_'.Auth::guard('clientuser')->user()->parent_phone))
            <li title="My Online Courses"><a href="{{ url('myCourses')}}"><i class="fa fa-circle-o"></i> My Online Courses</a></li>
            @endif
            <li title="My Course Results"><a href="{{ url('myCourseResults')}}"><i class="fa fa-circle-o"></i> My Course Results</a></li>
            <li title="My Certificate"><a href="{{ url('myCertificate')}}"><i class="fa fa-circle-o"></i> My Certificate</a></li>
            @if(!Session::has('parent_'.Auth::guard('clientuser')->user()->parent_phone))
            <li title="More Courses"><a href="{{ url('online-courses')}}" target="_blank"><i class="fa fa-circle-o"></i> More Courses</a></li>
            @endif
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
            @if(!Session::has('parent_'.Auth::guard('clientuser')->user()->parent_phone))
            <li title="My Test"><a href="{{ url('myTest')}}"><i class="fa fa-circle-o"></i> My Test</a></li>
            @endif
            <li title="My Test Results"><a href="{{ url('myTestResults')}}"><i class="fa fa-circle-o"></i> My Test Results</a></li>
            @if(!Session::has('parent_'.Auth::guard('clientuser')->user()->parent_phone))
            <li title="More Test"><a href="{{ url('online-tests')}}" target="_blank"><i class="fa fa-circle-o"></i> More Test</a></li>
            @endif
            <li title="My Offline Test Results"><a href="{{ url('myOfflineTestResults')}}"><i class="fa fa-circle-o"></i> My Offline Test Results</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-tasks"></i><b style="color: red;">{{Auth::guard('clientuser')->user()->unchecked_assignments}}</b><span>Assignment</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('myAssignments')}}"><i class="fa fa-circle-o"></i> My Assignments </a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user"></i> <span>Attendance</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('myAttendance')}}"><i class="fa fa-circle-o"></i> My Attendance </a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#" title="Message">
            <i class="fa fa-envelope"></i><b style="color: red;">{{Auth::guard('clientuser')->user()->unread_messages}}</b><span>Event/Message</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="My Event/Message"><a href="{{ url('myMessage')}}"><i class="fa fa-circle-o"></i> My Event/Message</a></li>
            <li title="My Individual Message"><a href="{{ url('myIndividualMessage')}}"><i class="fa fa-circle-o"></i> My Individual Message</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#" title="Calendar">
            <i class="fa fa-calendar"></i> <span>Calendar</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="My Calendar"><a href="{{ url('myCalendar')}}"><i class="fa fa-circle-o"></i> My Calendar</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#" title="Message">
            <i class="fa fa-inr"></i> <span>Payments</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="My Offline Payments"><a href="{{ url('myOfflinePayments')}}"><i class="fa fa-circle-o"></i>My Offline Payments</a></li>
            <li title="My Online Payments"><a href="{{ url('myOnlinePayments')}}"><i class="fa fa-circle-o"></i>My Online Payments</a></li>
            <li title="Uploaded Transactions"><a href="{{ url('uploadedTransactions')}}"><i class="fa fa-circle-o"></i>Uploaded Transactions</a></li>
          </ul>
        </li>
        @if(!Session::has('parent_'.Auth::guard('clientuser')->user()->parent_phone))
        <li class="treeview">
          <a href="#" title="Add Parent">
            <i class="fa fa-graduation-cap"></i> <span>Add Parent</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Add Parent"><a href="{{ url('myParent')}}"><i class="fa fa-circle-o"></i> Add Parent</a></li>
          </ul>
        </li>
        @endif
        <li class="treeview">
          <a href="#" title="Discussion">
            <i class="fa fa-comments-o"></i> <span>Discussion</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Discussion"><a href="{{ url('myDiscussion')}}"><i class="fa fa-circle-o"></i> Discussion</a></li>
          </ul>
        </li>
        <li class="header">Logout</li>
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
