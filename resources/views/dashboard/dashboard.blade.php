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
  @yield('dashboard_header')
</head>
<body class="hold-transition skin-blue sidebar-mini sidebar-collapse">
<div class="wrapper">
  <header class="main-header">
    <a href="" class="logo">
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
          @if(!empty(Auth::user()->photo))
            <img src="{{ asset(Auth::user()->photo)}} " class="img-circle" alt="User Image">
          @else
            <img src="{{ url('images/user/user.png')}}" class="img-circle" alt="User Image">
          @endif
        </div>
        <div class="pull-left info">
          <p>{{ ucfirst(Auth::user()->name)}}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          <input type="hidden" name="user_id" id="user_id" value="{{ (is_object(Auth::user()))?Auth::user()->id: NULL }}"/>
        </div>
      </div>
      <ul class="sidebar-menu">
        <li class="header">Vchip Technology</li>
        <li class="treeview ">
          <a href="#">
            <i class="fa fa-dashboard"></i> <span>Online Courses</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('myCourses')}}"><i class="fa fa-circle-o"></i> My Online Courses</a></li>
            <li><a href="{{ url('myCourseResults')}}"><i class="fa fa-circle-o"></i> My Course Results</a></li>
            <li><a href="{{ url('myCertificate')}}"><i class="fa fa-circle-o"></i> My Certificate</a></li>
            <li><a href="{{ url('courses')}}"><i class="fa fa-circle-o"></i> More Courses</a></li>
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
            <li><a href="{{ url('myTest')}}"><i class="fa fa-circle-o"></i> My Test</a></li>
            <li><a href="{{ url('myTestResults')}}"><i class="fa fa-circle-o"></i> My Test Results</a></li>
            <li><a href="{{ url('online-tests')}}"><i class="fa fa-circle-o"></i> More Test</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-pie-chart"></i>
            <span>Live Courses</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('myLiveCourses')}}"><i class="fa fa-circle-o"></i>My Live Courses</a></li>
            <li><a href="{{ url('liveCourse')}}"><i class="fa fa-circle-o"></i> More Courses</a></li>
          </ul>
        </li>
        <li class="treeview ">
          <a href="#">
            <i class="fa fa-laptop"></i>
            <span>My Webinar</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="#"><i class="fa fa-circle-o"></i> My Webinar</a></li>
            <li class="active"><a href="{{ url('webinar') }}"><i class="fa fa-circle-o"></i> more Webinar</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i> <span>Documents</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('myDocuments') }}"><i class="fa fa-circle-o"></i> Read Articles</a></li>
            <li><a href="{{ url('myFavouriteArticles') }}"><i class="fa fa-circle-o"></i> Favourite Articles</a></li>
            <li><a href="{{ url('documents') }}"><i class="fa fa-circle-o"></i> More Articles</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-table"></i> <span>Vkit</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('myVkits')}}"><i class="fa fa-circle-o"></i>  Favourite Projects</a></li>
            <li><a href="{{ url('vkits')}}"><i class="fa fa-circle-o"></i> More Projects</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-comments"></i> <span>Discussion</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('myQuestions')}}"><i class="fa fa-circle-o"></i> My Questions</a></li>
            <li><a href="{{ url('myReplies')}}"><i class="fa fa-circle-o"></i> My Replies</a></li>
            <li><a href="{{ url('discussion')}}"><i class="fa fa-circle-o"></i> More Discussion</a></li>
          </ul>
        </li>
        @if(3 == Auth::user()->user_type || 4 == Auth::user()->user_type || 5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
          <li class="treeview">
            <a href="#">
              <i class="fa fa-dashboard"></i> <span>Student Dashboard</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="{{ url('students')}}"><i class="fa fa-circle-o"></i>Students</a></li>
              <li><a href="{{ url('studentTestResults')}}"><i class="fa fa-circle-o"></i>Test Result</a></li>
              <li><a href="{{ url('studentCourses')}}"><i class="fa fa-circle-o"></i>Course</a></li>
                <li><a href="{{ url('studentPlacement')}}"><i class="fa fa-circle-o"></i>Placement</a></li>
              <!-- @if(5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
              @endif -->
            </ul>
          </li>
        @endif
        <li><a href="{{ url('profile')}}"><i class="fa fa-user"></i> <span>Profile</span></a></li>
        <li><a href="{{ url('/')}}"><i class="fa fa-home"></i> <span>Home</span></a></li>
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
    <section class="content">
      <div class="row">
        <div class="col-sm-9">
          @yield('dashboard_content')
        </div>
      </div>
    </section>
  </div>


</body>
</html>