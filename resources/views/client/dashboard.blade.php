<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Dashboard</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <script src="{{ asset('js/socket.io.js') }}"></script>
  <link href="{{ asset('css/bootstrap.min.css?ver=1.0')}}" rel="stylesheet">
  <link href="{{ asset('css/font-awesome/css/font-awesome.min.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/sidemenu/sidemenu_layout.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/sidemenu/_all-skins.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/v_main.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/comment.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/jquery-confirm.min.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css?family=Hind" rel="stylesheet">
  <link href="{{ asset('css/chat.css?ver=1.0')}}" rel="stylesheet"/>

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
  </style>
  @yield('dashboard_header')
</head>
<body class="hold-transition skin-blue sidebar-mini sidebar-collapse">
<div class="wrapper">
  <header class="main-header">
    <a href="" class="logo">
      <span class="logo-mini"><b></b></span>
      <span class="logo-lg"><b></b></span>
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
          <a href="{{ url('myprofile') }}">
          @if(!empty(Auth::guard('client')->user()->photo))
            <img src="{{ asset(Auth::guard('client')->user()->photo)}}" id="dashboardUserImage" class="img-circle" alt="User Image">
          @else
            <img src="{{ asset('images/user/user1.png')}}" id="dashboardUserImage" class="img-circle" alt="User Image">
          @endif
          </a>
        </div>
        <div class="pull-left info">
          <p>{{ ucfirst(Auth::guard('client')->user()->name)}}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          <input type="hidden" name="user_id" id="user_id" value="{{ (is_object(Auth::guard('client')->user()))?Auth::guard('client')->user()->id: NULL }}"/>
        </div>
      </div>
      <ul class="sidebar-menu">
        <li class="header">Home Page</li>
        <li class="treeview ">
          <a href="#">
            <i class="fa fa-comments"></i><span> Chat Messages </span><b style="color: red;" id="unreadCountDash_{{$subdomainName}}_1_{{Auth::guard('client')->user()->id}}">{{Auth::guard('client')->user()->unreadChatMessagesCount()}}</b>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Chat Messages"><a href="{{ url('allChatMessages')}}"><i class="fa fa-circle-o"></i> Chat Messages : <b style="color: red;" id="unreadCountDash_{{$subdomainName}}_2_{{Auth::guard('client')->user()->id}}">{{Auth::guard('client')->user()->unreadChatMessagesCount()}}</b></a></li>
          </ul>
        </li>
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
              @if(1 == $subdomain->course_show_hide)
               <li><a href="#courses1" title="Online Courses"><i class="fa fa-circle-o"></i> Online Courses
                  <span class="pull-right-container">
                   <span class="label label-primary pull-right" data-toggle="tooltip"  title="Remove">
                    <i id="courses" class="fa fa-times remove"></i>
                   </span>
                  </span></a>
                </li>
              @endif
              @if(1 == $subdomain->test_show_hide)
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
                @if(0 == $subdomain->course_show_hide)
                <li><a href="#courses" title="Online Courses"><i class="fa fa-circle-o"></i> Online Courses
                  <span class="pull-right-container">
                   <span class="label label-primary pull-right" data-toggle="tooltip"  title="Add">
                    <i id="courses" class="fa fa-check add"></i>
                   </span>
                  </span></a>
                </li>
                @endif
                @if(0 == $subdomain->test_show_hide)
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
        <li class="header">Dashboard</li>
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
        <li class="treeview">
          <a href="#" title="Discussion">
            <i class="fa fa-comments-o"></i> <span>Discussion</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Manage Discussion Category"><a href="{{ url('manageDiscussionCategory')}}"><i class="fa fa-circle-o"></i> Discussion Category </a></li>
            <li title="Discussion"><a href="{{ url('manageDiscussion')}}"><i class="fa fa-circle-o"></i> Discussion</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#" title="Users Info">
            <i class="fa fa-group"></i> <span>Users Info</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Add Users "><a href="{{ url('addUsers')}}"><i class="fa fa-circle-o"></i> Add Users </a></li>
            <li title="Show All Users "><a href="{{ url('allUsers')}}"><i class="fa fa-circle-o"></i> All Users </a></li>
            <li title="Show Users Test Results"><a href="{{ url('userTestResults')}}"><i class="fa fa-circle-o"></i> User Test Results </a></li>
            <li title="Show User Courses"><a href="{{ url('userCourses')}}"><i class="fa fa-circle-o"></i> User Courses </a></li>
            <li title="Show User Placement"><a href="{{ url('userPlacement')}}"><i class="fa fa-circle-o"></i> User Placement </a></li>
            <li title="Show User Video"><a href="{{ url('userVideo')}}"><i class="fa fa-circle-o"></i> User Video </a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#" title="Teachers Info">
            <i class="fa fa-graduation-cap"></i> <span>Teachers Info</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Add Teachers "><a href="{{ url('addTeachers')}}"><i class="fa fa-circle-o"></i> Add Teachers </a></li>
            <li title="All Teachers "><a href="{{ url('allTeachers')}}"><i class="fa fa-circle-o"></i> All Teachers </a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#" title="Calendar Management">
            <i class="fa fa-calendar"></i> <span>Calendar Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Manage Classes"><a href="{{ url('manageClasses')}}"><i class="fa fa-circle-o"></i> Manage Classes </a></li>
            <li title="Manage Exams"><a href="{{ url('manageExams')}}"><i class="fa fa-circle-o"></i> Manage Exams </a></li>
            <li title="Manage Holidays"><a href="{{ url('manageHolidays')}}"><i class="fa fa-circle-o"></i> Manage Holidays </a></li>
            <li title="Manage Notices"><a href="{{ url('manageNotices')}}"><i class="fa fa-circle-o"></i> Manage Notices </a></li>
            <li title="Batch Schedules"><a href="{{ url('manageSchedules')}}"><i class="fa fa-circle-o"></i> Batch Schedules </a></li>
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
            <li title="Show All Test Results"><a href="{{ url('allTestResults')}}"><i class="fa fa-circle-o"></i> All Test Results </a></li>
          </ul>
        </li>
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
        <li class="treeview">
          <a href="#" title="Event/Message">
            <i class="fa fa-envelope"></i> <span>Event/Message</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Manage Event/Message"><a href="{{ url('manageMessage')}}"><i class="fa fa-circle-o"></i> Manage Event/Message</a></li>
            <li title="Individual Message"><a href="{{ url('manageIndividualMessage')}}"><i class="fa fa-circle-o"></i> Individual Message</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#" title="Plans & Billing">
            <i class="fa fa-inr"></i> <span>Plans & Billing</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Plans"><a href="{{ url('managePlans')}}"><i class="fa fa-circle-o"></i> Plans </a></li>
            <li title="Billing"><a href="{{ url('manageBillings')}}"><i class="fa fa-circle-o"></i> Billing </a></li>
            <li title="History"><a href="{{ url('manageHistory')}}"><i class="fa fa-circle-o"></i> History </a></li>
            <li title="Bank Details"><a href="{{ url('manageBankDetails')}}"><i class="fa fa-circle-o"></i> Bank Details </a></li>
            <li title="User Payments"><a href="{{ url('manageUserPayments')}}"><i class="fa fa-circle-o"></i> User Payments </a></li>
            <li title="Purchase Sms"><a href="{{ url('managePurchaseSms')}}"><i class="fa fa-circle-o"></i> Purchase Sms </a></li>
            <li title="Receipt Details"><a href="{{ url('manageReceipt')}}"><i class="fa fa-circle-o"></i> Receipt Details </a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#" title="Offline Payments">
            <i class="fa fa-money" aria-hidden="true"></i> <span>Offline Payments</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Manage Offline Payments"><a href="{{ url('manageOfflinePayments')}}"><i class="fa fa-circle-o"></i>Manage Offline Payments </a></li>
            <li title="Batch Payments"><a href="{{ url('batchPayments')}}"><i class="fa fa-circle-o"></i>Batch Payments </a></li>
            <li title="Due Payments"><a href="{{ url('duePayments')}}"><i class="fa fa-circle-o"></i>Due Payments </a></li>
            <li title="Uploaded Transactions"><a href="{{ url('userUploadedTransactions')}}"><i class="fa fa-circle-o"></i>Uploaded Transactions</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#" title="Market Palce">
            <i class="fa fa-shopping-cart"></i> <span>Market Palce</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Payable SubCategory"><a href="{{ url('managePayableSubCategory')}}"><i class="fa fa-circle-o"></i> Payable SubCategory </a></li>
            <li title="Purchased SubCategory"><a href="{{ url('managePurchasedSubCategory')}}"><i class="fa fa-circle-o"></i> Purchased SubCategory </a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#" title="Settings">
            <i class="fa fa-cog"></i> <span>Settings</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Manage Settings"><a href="{{ url('manageSettings')}}"><i class="fa fa-circle-o"></i> Manage Settings</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#" title="Gallery">
            <i class="fa fa-picture-o"></i> <span>Gallery</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Manage Gallery Types"><a href="{{ url('manageGalleryTypes')}}"><i class="fa fa-circle-o"></i> Manage Gallery Types</a></li>
            <li title="Manage Gallery Images"><a href="{{ url('manageGalleryImages')}}"><i class="fa fa-circle-o"></i> Manage Gallery Images</a></li>
          </ul>
        </li>
        <li class="header">Logout</li>
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
        }, 50000); // <-- time in milliseconds
    });
</script>
</body>
</html>
