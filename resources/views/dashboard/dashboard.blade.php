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

  <script src="{{ asset('js/socket.io.js') }}"></script>
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
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
          <a href="{{ url('college/'.Session::get('college_user_url').'/profile')}}">
          @if(is_file(Auth::user()->photo) || (!empty(Auth::user()->photo) && false == preg_match('/userStorage/',Auth::user()->photo)))
            <img src="{{ asset(Auth::user()->photo)}}" id="dashboardUserImage" class="img-circle" alt="User Image">
          @else
            <img src="{{ url('images/user/user1.png')}}" id="dashboardUserImage" class="img-circle" alt="User Image">
          @endif
          </a>
          @php
            $selectedUserType = Session::get('selected_user_type');
          @endphp
        </div>
        <div class="pull-left info">
          <p><a href="{{ url('profile')}}">{{ ucfirst(Auth::user()->name)}}</a></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          <input type="hidden" name="user_id" id="user_id" value="{{ (is_object(Auth::user()))?Auth::user()->id: NULL }}"/>
        </div>
      </div>
      <ul class="sidebar-menu">
        <li class="header">Vchip Technology</li>
        <li class="treeview ">
          <a href="#">
            <i class="fa fa-comments"></i><span> Chat Messages </span><b style="color: red;" id="unreadCountDash_1_{{Auth::user()->id}}">{{Auth::user()->unreadChatMessagesCount()}}</b>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('college/'.Session::get('college_user_url').'/allChatMessages')}}"><i class="fa fa-circle-o"></i> Chat Messages : <b style="color: red;" id="unreadCountDash_2_{{Auth::user()->id}}">{{Auth::user()->unreadChatMessagesCount()}}</b></a></li>
          </ul>
        </li>
        @if(2 == Auth::user()->user_type)
        <li class="treeview ">
          <a href="#">
            <i class="fa fa-star"></i><span>Notifications </span><b style="color: red;">{{Auth::user()->userNotificationCount()}}</b>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('college/'.Session::get('college_user_url').'/myNotifications')}}"><i class="fa fa-circle-o"></i> My Notifications : <b style="color: red;">{{Auth::user()->userNotificationCount()}} </b></a></li>
            <li><a href="{{ url('college/'.Session::get('college_user_url').'/adminMessages')}}"><i class="fa fa-circle-o"></i> Admin Messages : <b style="color: red;">{{Auth::user()->adminNotificationCount()}} </b></a></li>
          </ul>
        </li>
        @endif
        @if(3 == Auth::user()->user_type || 4 == Auth::user()->user_type || 5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
        <li class="treeview">
          <a href="#">
            <i class="fa fa-calendar"></i> <span>Academic</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @if(4 == Auth::user()->user_type || 5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageCollegeCategory')}}"><i class="fa fa-circle-o"></i> Manage Category</a></li>
            @endif
            <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageCollegeSubject')}}"><i class="fa fa-circle-o"></i> Manage Subject</a></li>
            <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageCollegeAttendance')}}"><i class="fa fa-circle-o"></i> Manage Attendance</a></li>
            <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageCollegeOfflinePaper')}}"><i class="fa fa-circle-o"></i> Manage Offline Paper</a></li>
            <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageCollegeOfflineExam')}}"><i class="fa fa-circle-o"></i> Manage Offline Exam</a></li>
          </ul>
        </li>
        @endif
        <li class="treeview ">
          <a href="#">
            <i class="fa fa-dashboard"></i> <span>Online Courses</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @if(3 == Auth::user()->user_type || 4 == Auth::user()->user_type || 5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
              @if(4 == Auth::user()->user_type || 5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
                <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageCourseAll')}}"><i class="fa fa-circle-o"></i> Manage All </a></li>
                <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageCourseSubCategory')}}"><i class="fa fa-circle-o"></i> Manage Sub Category </a></li>
              @endif
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageCourseCourse')}}"><i class="fa fa-circle-o"></i> Manage Course </a></li>
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageCourseVideo')}}"><i class="fa fa-circle-o"></i> Manage Video </a></li>
            @elseif(2 == Auth::user()->user_type)
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/myCollegeCourses')}}"><i class="fa fa-circle-o"></i> My Online Courses</a></li>
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/myCollegeCourseResults')}}"><i class="fa fa-circle-o"></i> My Course Results</a></li>
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/myCertificate')}}"><i class="fa fa-circle-o"></i> My Certificate</a></li>
              <li><a href="{{ url('courses')}}" target="_blank" ><i class="fa fa-circle-o"></i> More Courses</a></li>
            @endif
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Online Test</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @if(3 == Auth::user()->user_type || 4 == Auth::user()->user_type || 5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
              @if(4 == Auth::user()->user_type || 5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
                <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageTestAll')}}"><i class="fa fa-circle-o"></i> Manage All </a></li>
                <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageSubCategory')}}"><i class="fa fa-circle-o"></i> Manage Sub Category </a></li>
              @endif
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageSubject')}}"><i class="fa fa-circle-o"></i> Manage Subject </a></li>
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/managePaper')}}"><i class="fa fa-circle-o"></i> Manage Paper </a></li>
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageQuestions')}}"><i class="fa fa-circle-o"></i> Manage Question </a></li>
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/showQuestionBank')}}"><i class="fa fa-circle-o"></i> Question Bank </a></li>
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/uploadCollegeQuestions')}}"><i class="fa fa-circle-o"></i> Upload Excel File </a></li>
            @elseif(2 == Auth::user()->user_type)
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/myCollegeTest')}}"><i class="fa fa-circle-o"></i> My Test</a></li>
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/myCollegeTestResults')}}"><i class="fa fa-circle-o"></i> My Test Results</a></li>
              <li><a href="{{ url('online-tests')}}" target="_blank" ><i class="fa fa-circle-o"></i> More Test</a></li>
            @endif
          </ul>
        </li>
        @if(2 == Auth::user()->user_type)
        <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i> <span>Documents</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('college/'.Session::get('college_user_url').'/myDocuments') }}"><i class="fa fa-circle-o"></i> Read Articles</a></li>
            <li><a href="{{ url('college/'.Session::get('college_user_url').'/myFavouriteArticles') }}"><i class="fa fa-circle-o"></i> Favourite Articles</a></li>
            <li><a href="{{ url('documents') }}" target="_blank" ><i class="fa fa-circle-o"></i> More Articles</a></li>
          </ul>
        </li>
        @endif
        @if(2 == Auth::user()->user_type || 3 == Auth::user()->user_type || 4 == Auth::user()->user_type || 5 == Auth::user()->user_type)
        <li class="treeview">
          <a href="#">
            <i class="fa fa-table"></i> <span>Vkit</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @if(3 == Auth::user()->user_type || 4 == Auth::user()->user_type || 5 == Auth::user()->user_type)
              <!-- <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageVkitCategory')}}"><i class="fa fa-circle-o"></i>  Manage Category</a></li> -->
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageVkitProject')}}" ><i class="fa fa-circle-o"></i> Manage Project</a></li>
            @elseif(2 == Auth::user()->user_type)
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/myCollegeVkits')}}"><i class="fa fa-circle-o"></i>  Favourite Projects</a></li>
              <li><a href="{{ url('vkits')}}" target="_blank" ><i class="fa fa-circle-o"></i> More Projects</a></li>
            @endif
          </ul>
        </li>
        @endif
        <li class="treeview">
          <a href="#">
            <i class="fa fa-comments"></i> <span>Discussion</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('college/'.Session::get('college_user_url').'/discussion')}}"><i class="fa fa-circle-o"></i> Discussion</a></li>
            <!-- <li><a href="{{ url('college/'.Session::get('college_user_url').'/myQuestions')}}"><i class="fa fa-circle-o"></i> My Questions</a></li>
            <li><a href="{{ url('college/'.Session::get('college_user_url').'/myReplies')}}"><i class="fa fa-circle-o"></i> My Replies</a></li>
            <li><a href="{{ url('discussion')}}" target="_blank" ><i class="fa fa-circle-o"></i> More Discussion</a></li> -->
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
            @if(3 == Auth::user()->user_type || 4 == Auth::user()->user_type || 5 == Auth::user()->user_type|| 6 == Auth::user()->user_type)
                <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageAssignmentTopic')}}"><i class="fa fa-circle-o"></i> Manage Topic</a></li>
                <li><a href="{{ url('college/'.Session::get('college_user_url').'/manageAssignment')}}"><i class="fa fa-circle-o"></i> Manage Assignment</a></li>
                <li><a href="{{ url('college/'.Session::get('college_user_url').'/studentsAssignment')}}"><i class="fa fa-circle-o"></i> Students Assignment</a></li>
            @elseif(2 == Auth::user()->user_type)
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/myAssignments')}}"><i class="fa fa-circle-o"></i> My Assignments</a></li>
            @endif
          </ul>
        </li>
        @if(2 == Auth::user()->user_type)
          <li class="treeview">
            <a href="#">
              <i class="fa fa-group"></i> <span>Users Info</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/studentCollegePlacement')}}"><i class="fa fa-circle-o"></i>Placement</a></li>
            </ul>
          </li>
        @endif
        @if(3 == Auth::user()->user_type || 4 == Auth::user()->user_type || 5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
          <li class="treeview">
            <a href="#">
              <i class="fa fa-group"></i> <span>Users Info</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/students')}}"><i class="fa fa-circle-o"></i>Users</a></li>
              @if(2 == $selectedUserType)
                <li><a href="{{ url('college/'.Session::get('college_user_url').'/studentCollegeTestResults')}}"><i class="fa fa-circle-o"></i>Test Result</a></li>
                <li><a href="{{ url('college/'.Session::get('college_user_url').'/studentCollegeCourses')}}"><i class="fa fa-circle-o"></i>Course</a></li>
              @elseif(3 == $selectedUserType || 4 == $selectedUserType)
                <li><a href="{{ url('college/'.Session::get('college_user_url').'/lecturerPapers')}}"><i class="fa fa-circle-o"></i>Lecturer Papers</a></li>
                <li><a href="{{ url('college/'.Session::get('college_user_url').'/lecturerCourses')}}"><i class="fa fa-circle-o"></i>Lecturer Courses</a></li>
              @endif
               @if(6 == Auth::user()->user_type)
                <li><a href="{{ url('college/'.Session::get('college_user_url').'/studentVideo')}}"><i class="fa fa-circle-o"></i> Video </a></li>
              @endif
              <li><a href="{{ url('college/'.Session::get('college_user_url').'/studentCollegePlacement')}}"><i class="fa fa-circle-o"></i>Placement</a></li>
            </ul>
          </li>
          <li class="treeview">
            <a href="#" title="All Test Results">
              <i class="fa fa-trophy"></i> <span>All Test Results</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Show College Test Results"><a href="{{ url('college/'.Session::get('college_user_url').'/collegeTestResults')}}"><i class="fa fa-circle-o"></i> All Test Results </a></li>
            </ul>
          </li>
        @endif
        <!-- <li><a href="{{ url('profile')}}"><i class="fa fa-user"></i> <span>Profile</span></a></li>
        <li><a href="{{ url('/')}}"><i class="fa fa-home"></i> <span>Home</span></a></li> -->
        <li class="header">LABELS</li>
        <li>
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
