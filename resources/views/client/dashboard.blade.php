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
  .admin_table,.admin_div{
    padding-top: 10px;
    background-color: #01bafd;
  }
  </style>
  @yield('dashboard_header')
</head>
<body class="hold-transition skin-blue sidebar-mini sidebar-collapse">
<div class="wrapper">
  <header class="main-header">
    <a href="" class="logo">
      <span class="logo-mini"><b> V</b>EDU</span>
      <span class="logo-lg"><b>Vchip</b>TECHNOLOGY</span>
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
          <img src="{{ asset('images/user.png')}}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{ ucfirst(Auth::guard('client')->user()->name)}}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          <input type="hidden" name="user_id" id="user_id" value="{{ (is_object(Auth::guard('client')->user()))?Auth::guard('client')->user()->id: NULL }}"/>
        </div>
      </div>
      <!-- <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
            <span class="input-group-btn">
              <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
              </button>
            </span>
        </div>
      </form> -->
      <ul class="sidebar-menu">
        <li class="header">Home Page</li>

         <li class="treeview">
          <a href="#" title="Home Page">
            <i class="fa fa-home"></i> <span>Home Page</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
              <li>
                <a href="{{ url('manageClientHome')}}" title="Show/Edit Home Page">
                  <i class="fa fa-circle-o" aria-hidden="true"></i><span>Show/Edit Home Page</span>
                  <span class="pull-right-container"></span>
                </a>
              </li>
               <!-- <li><a href="#nav-logo"><i class="fa fa-circle-o"></i> Nav-logo</a></li> -->
                <li><a href="#vchip-header" title="Header"><i class="fa fa-circle-o"></i>Header</a></li>
                @if(1 == $subdomain->about_show_hide)
                <li><a href="#about1" title="About us"><i class="fa fa-circle-o"></i>About us
                <span class="pull-right-container">
                   <span class="label label-primary pull-right" data-toggle="tooltip"  title="Remove">
                    <i id="about" class="fa fa-times remove"></i>
                   </span>
                  </span></a>
                </li>
                @endif
              @if(1 == $subdomain->course_show_hide && 1 == $client->course_permission)
               <li><a href="#courses1" title="Online Courses"><i class="fa fa-circle-o"></i> Online Courses
                  <span class="pull-right-container">
                   <span class="label label-primary pull-right" data-toggle="tooltip"  title="Remove">
                    <i id="courses" class="fa fa-times remove"></i>
                   </span>
                  </span></a>
                </li>
              @endif
              @if(1 == $subdomain->test_show_hide && 1 == $client->test_permission)
               <li><a href="#test1" title="Online Test-series"><i class="fa fa-circle-o"></i> Online Test-series
                 <span class="pull-right-container">
                     <span class="label label-primary pull-right" data-toggle="tooltip" title="Remove">
                      <i id="test" class="fa fa-times remove"></i>
                     </span>
                 </span></a>
               </li>
              @endif
              @if(1 == $subdomain->customer_show_hide)
                <li><a href="#customer1" title="Our customer"><i class="fa fa-circle-o"></i>Our customer
                  <span class="pull-right-container">
                   <span class="label label-primary pull-right" data-toggle="tooltip" title="Remove">
                    <i id="customer" class="fa fa-times remove"></i>
                   </span>
                  </span></a>
                </li>
              @endif
              @if(1 == $subdomain->testimonial_show_hide)
                <li><a href="#testimonial1" title="Testimonial"><i class="fa fa-circle-o"></i>Testimonial
                  <span class="pull-right-container">
                   <span class="label label-primary pull-right" data-toggle="tooltip"  title="Remove">
                    <i id="testimonial" class="fa fa-times remove"></i>
                   </span>
                  </span></a>
                </li>
              @endif
              @if(1 == $subdomain->team_show_hide)
                <li><a href="#team1" title="Our Team"><i class="fa fa-circle-o"></i>Our Team
                  <span class="pull-right-container">
                   <span class="label label-primary pull-right" data-toggle="tooltip"  title="Remove">
                    <i id="team" class="fa fa-times remove"></i>
                   </span>
                  </span></a>
                </li>
              @endif
              <li><a href="#footer" title="Footer"><i class="fa fa-circle-o"></i>Footer</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#" title="Add Section">
            <i class="fa fa-plus-square"></i> <span>Add Section</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
           <ul class="treeview-menu">
                @if(0 == $subdomain->about_show_hide)
                <li><a href="#about2" title="About us"><i class="fa fa-circle-o"></i>About us
                <span class="pull-right-container">
                   <span class="label label-primary pull-right" data-toggle="tooltip"  title="Add">
                    <i id="about" class="fa fa-check add"></i>
                   </span>
                  </span></a>
                </li>
                @endif
                @if(0 == $subdomain->course_show_hide && 1 == $client->course_permission)
                <li><a href="#courses" title="Online Courses"><i class="fa fa-circle-o"></i> Online Courses
                  <span class="pull-right-container">
                   <span class="label label-primary pull-right" data-toggle="tooltip"  title="Add">
                    <i id="courses" class="fa fa-check add"></i>
                   </span>
                  </span></a>
                </li>
                @endif
                @if(0 == $subdomain->test_show_hide && 1 == $client->test_permission)
                <li><a href="#test" title="Online Test-series"><i class="fa fa-circle-o"></i> Online Test-series
                 <span class="pull-right-container">
                     <span class="label label-primary pull-right" data-toggle="tooltip" title="Add">
                      <i id="test" class="fa fa-check add"></i>
                     </span>
                 </span></a>
                </li>
                @endif
                @if(0 == $subdomain->customer_show_hide)
                <li><a href="#customer" title="Our customer"><i class="fa fa-circle-o"></i>Our customer
                  <span class="pull-right-container">
                   <span class="label label-primary pull-right" data-toggle="tooltip" title="Add">
                    <i id="customer" class="fa fa-check add"></i>
                   </span>
                  </span></a>
                </li>
                @endif
                @if(0 == $subdomain->testimonial_show_hide)
                <li><a href="#testimonial" title="Testimonial"><i class="fa fa-circle-o"></i>Testimonial
                  <span class="pull-right-container">
                   <span class="label label-primary pull-right" data-toggle="tooltip"  title="Add">
                    <i id="testimonial" class="fa fa-check add"></i>
                   </span>
                  </span></a>
                </li>
                @endif
                @if(0 == $subdomain->team_show_hide)
                <li><a href="#team" title="Our Team"><i class="fa fa-circle-o"></i>Our Team
                  <span class="pull-right-container">
                   <span class="label label-primary pull-right" data-toggle="tooltip"  title="Add">
                    <i id="team" class="fa fa-check add"></i>
                   </span>
                  </span></a>
                </li>
                @endif
            </ul>
        </li>
        <li class="header">Vchip TECHNOLOGY</li>
        <li class="treeview">
          <a href="#" title="Institute Courses">
            <i class="fa fa-files-o"></i>
            <span>Institute Courses</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Manage Courses"><a href="{{ url('manageInstituteCourses')}}"><i class="fa fa-circle-o"></i> Manage Courses </a></li>
          </ul>
        </li>
        @if(1 == $client->course_permission)
        <li class="treeview ">
          <a href="#" title="Online Courses">
            <i class="fa fa-dashboard"></i> <span>Online Courses</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Manage Category"><a href="{{ url('manageOnlineCategory')}}"><i class="fa fa-circle-o"></i> Manage Category </a></li>
              <li title="Manage Sub Category"><a href="{{ url('manageOnlineSubCategory')}}"><i class="fa fa-circle-o"></i> Manage Sub Category </a></li>
              <li title="Manage Course"><a href="{{ url('manageOnlineCourse')}}"><i class="fa fa-circle-o"></i> Manage Course </a></li>
              <li title="Manage Video"><a href="{{ url('manageOnlineVideo')}}"><i class="fa fa-circle-o"></i> Manage Video </a></li>
          </ul>
        </li>
        @endif
        @if(1 == $client->test_permission)
        <li class="treeview">
          <a href="#" title="Online Test">
            <i class="fa fa-files-o"></i>
            <span>Online Test</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Manage Category"><a href="{{ url('manageOnlineTestCategory')}}"><i class="fa fa-circle-o"></i> Manage Category </a></li>
              <li title="Manage Sub Category"><a href="{{ url('manageOnlineTestSubCategory')}}"><i class="fa fa-circle-o"></i> Manage Sub Category </a></li>
              <li title="Manage Subject"><a href="{{ url('manageOnlineTestSubject')}}"><i class="fa fa-circle-o"></i> Manage Subject </a></li>
              <li title="Manage Paper"><a href="{{ url('manageOnlineTestSubjectPaper')}}"><i class="fa fa-circle-o"></i> Manage Paper </a></li>
              <li title="Manage Question"><a href="{{ url('manageOnlineTestQuestion')}}"><i class="fa fa-circle-o"></i> Manage Question </a></li>
          </ul>
        </li>
        @endif
        <li class="treeview">
            <a href="#" title="Users Info">
              <i class="fa fa-group"></i> <span>Users Info</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Show All Users "><a href="{{ url('allUsers')}}"><i class="fa fa-circle-o"></i> All Users </a></li>
              <li title="Show Users Test Results"><a href="{{ url('userTestResults')}}"><i class="fa fa-circle-o"></i> User Test Results </a></li>
              <li title="Show User Courses"><a href="{{ url('userCourses')}}"><i class="fa fa-circle-o"></i> User Courses </a></li>
            </ul>
          </li>
        <li title="Home"><a href="{{ url('/')}}"><i class="fa fa-dashboard"></i> <span>Home</span></a></li>
        <li class="header">LABELS</li>
        <li title="Logout">
          <a href="{{ url('client/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-sign-out" aria-hidden="true"></i> <span>Logout </span>
            <span class="pull-right-container"></span>
          </a>
          <form id="logout-form" action="{{ url('client/logout') }}" method="POST" style="display: none;">
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