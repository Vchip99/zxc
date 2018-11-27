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
      <span class="logo-mini"><b> Home</b></span>
      <span class="logo-lg"><b>Home</b></span>
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
          @if(!empty(Auth::guard('clientuser')->user()->photo) && is_file(Auth::guard('clientuser')->user()->photo))
            <img src="{{ asset(Auth::guard('clientuser')->user()->photo)}}" class="img-circle" alt="User Image" >
          @else
            <img src="{{ asset('images/user1.png')}}" class="img-circle" alt="User Image">
          @endif
          </a>
        </div>
        @php
          if(!empty(Auth::guard('clientuser')->user()->assigned_modules)){
            $assignedModulesArr = explode(',', Auth::guard('clientuser')->user()->assigned_modules);
          } else {
            $assignedModulesArr = [];
          }
        @endphp
        <div class="pull-left info">
          <p><a href="{{ url('profile')}}">{{ ucfirst(Auth::guard('clientuser')->user()->name)}}</a></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          <input type="hidden" name="user_id" id="user_id" value="{{ (is_object(Auth::guard('clientuser')->user()))?Auth::guard('clientuser')->user()->id: NULL }}"/>
        </div>
      </div>
      <ul class="sidebar-menu">
        <li class="header">Dashboard</li>
        @if(in_array(1,$assignedModulesArr))
        <li class="treeview ">
          <a href="#" title="Online Courses">
            <i class="fa fa-dashboard"></i> <span>Online Courses</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Manage All"><a href="{{ url('manageAllCourse')}}"><i class="fa fa-circle-o"></i> Manage All </a></li>
            <li title="Manage Category"><a href="{{ url('manageOnlineCategory')}}"><i class="fa fa-circle-o"></i> Manage Category </a></li>
            <li title="Manage Sub Category"><a href="{{ url('manageOnlineSubCategory')}}"><i class="fa fa-circle-o"></i> Manage Sub Category </a></li>
            <li title="Manage Course"><a href="{{ url('manageOnlineCourse')}}"><i class="fa fa-circle-o"></i> Manage Course </a></li>
            <li title="Manage Video"><a href="{{ url('manageOnlineVideo')}}"><i class="fa fa-circle-o"></i> Manage Video </a></li>
          </ul>
        </li>
        @endif
        @if(in_array(2,$assignedModulesArr))
        <li class="treeview">
          <a href="#" title="Online Test">
            <i class="fa fa-files-o"></i>
            <span>Online Test</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Manage All"><a href="{{ url('manageAllTest')}}"><i class="fa fa-circle-o"></i> Manage All </a></li>
            <li title="Manage Category"><a href="{{ url('manageOnlineTestCategory')}}"><i class="fa fa-circle-o"></i> Manage Category </a></li>
            <li title="Manage Sub Category"><a href="{{ url('manageOnlineTestSubCategory')}}"><i class="fa fa-circle-o"></i> Manage Sub Category </a></li>
            <li title="Manage Subject"><a href="{{ url('manageOnlineTestSubject')}}"><i class="fa fa-circle-o"></i> Manage Subject </a></li>
            <li title="Manage Paper"><a href="{{ url('manageOnlineTestSubjectPaper')}}"><i class="fa fa-circle-o"></i> Manage Paper </a></li>
            <li title="Manage Question"><a href="{{ url('manageOnlineTestQuestion')}}"><i class="fa fa-circle-o"></i> Manage Question </a></li>
            <li title="Question Bank"><a href="{{ url('manageQuestionBank')}}"><i class="fa fa-circle-o"></i> Question Bank </a></li>
            <li title="Upload Excel File"><a href="{{ url('manageUploadQuestions')}}"><i class="fa fa-circle-o"></i> Upload Excel File </a></li>
          </ul>
        </li>
        @endif
        @if(in_array(3,$assignedModulesArr))
        <li class="treeview">
          <a href="#" title="Users Info">
            <i class="fa fa-group"></i> <span>Users Info</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Show Users Test Results"><a href="{{ url('userTestResults')}}"><i class="fa fa-circle-o"></i> User Test Results </a></li>
            <li title="Show User Courses"><a href="{{ url('userCourses')}}"><i class="fa fa-circle-o"></i> User Courses </a></li>
            <li title="Show User Placement"><a href="{{ url('userPlacement')}}"><i class="fa fa-circle-o"></i> User Placement </a></li>
            <li title="Show User Video"><a href="{{ url('userVideo')}}"><i class="fa fa-circle-o"></i> User Video </a></li>
          </ul>
        </li>
        @endif
        @if(in_array(4,$assignedModulesArr))
        <li class="treeview">
          <a href="#" title="All Test Results">
            <i class="fa fa-trophy"></i> <span>All Test Results</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Show All Test Results"><a href="{{ url('allTestResults')}}"><i class="fa fa-circle-o"></i> All Test Results </a></li>
          </ul>
        </li>
        @endif
        @if(in_array(5,$assignedModulesArr))
        <li class="treeview">
          <a href="#" title="Batch">
            <i class="fa fa-address-book"></i> <span>Batch</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Manage Batch"><a href="{{ url('manageBatch')}}"><i class="fa fa-circle-o"></i> Manage Batch</a></li>
            <li title="Batch Students"><a href="{{ url('associateBatchStudents')}}"><i class="fa fa-circle-o"></i> Batch Students</a></li>
            <li title="Attendance Calendar"><a href="{{ url('manageAttendanceCalendar')}}"><i class="fa fa-circle-o"></i> Attendance Calendar</a></li>
            <li title="Update Offline Marks"><a href="{{ url('manageExamMarks')}}"><i class="fa fa-circle-o"></i> Update Offline Marks</a></li>
          </ul>
        </li>
        @endif
        @if(in_array(6,$assignedModulesArr))
        <li class="treeview">
          <a href="#" title="Assignment">
            <i class="fa fa-tasks"></i> <span>Assignment</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Manage Subject"><a href="{{ url('manageAssignmentSubject')}}"><i class="fa fa-circle-o"></i> Manage Subject</a></li>
            <li title="Manage Topic"><a href="{{ url('manageAssignmentTopic')}}"><i class="fa fa-circle-o"></i> Manage Topic</a></li>
            <li title="Manage Assignment"><a href="{{ url('manageAssignment')}}"><i class="fa fa-circle-o"></i> Manage Assignment</a></li>
            <li title="Students Assignment"><a href="{{ url('studentsAssignment')}}"><i class="fa fa-circle-o"></i>Students Assignment</a></li>
          </ul>
        </li>
        @endif
        @if(in_array(7,$assignedModulesArr))
        <li class="treeview">
          <a href="#" title="Event/Message">
            <i class="fa fa-envelope"></i> <span>Event/Message</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Manage Event/Message"><a href="{{ url('manageMessage')}}"><i class="fa fa-circle-o"></i> Manage Event/Message</a></li>
          </ul>
        </li>
        @endif
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
