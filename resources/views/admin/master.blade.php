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
  <link href="{{ asset('css/jquery-confirm.min.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css?family=Hind" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Noto+Sans&subset=devanagari" rel="stylesheet">

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
        <a href="">
          <img src="{{ asset('images/user1.png')}}" class="img-circle" alt="User Image">
        </a>
        </div>
        <div class="pull-left info">
          @php
            $adminUser = Auth::guard('admin')->user();
            $collegeUserType = Session::get('admin_selected_user_type');
          @endphp
          <p>{{ucfirst($adminUser->name)}}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <ul class="sidebar-menu">
        <li class="header">Vchip Technology - Admin</li>
          <li class="treeview ">
            <a href="#" title="Online Courses">
              <i class="fa fa-dashboard"></i> <span>Online Courses</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              @if($adminUser->hasRole('admin'))
                <li title="Manage All"><a href="{{ url('admin/manageCourseAll')}}"><i class="fa fa-circle-o"></i> Manage All </a></li>
                <li title="Manage Category"><a href="{{ url('admin/manageCourseCategory')}}"><i class="fa fa-circle-o"></i> Manage Category </a></li>
                <li title="Manage Sub Category"><a href="{{ url('admin/manageCourseSubCategory')}}"><i class="fa fa-circle-o"></i> Manage Sub Category </a></li>
              @endif
              @if($adminUser->hasRole('admin') || $adminUser->hasRole('sub-admin'))
                  <li title="Manage Course"><a href="{{ url('admin/manageCourseCourse')}}"><i class="fa fa-circle-o"></i> Manage Course </a></li>
                  <li title="Manage Video"><a href="{{ url('admin/manageCourseVideo')}}"><i class="fa fa-circle-o"></i> Manage Video </a></li>
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
              @if($adminUser->hasRole('admin'))
                <li title="Manage All"><a href="{{ url('admin/manageTestAll')}}"><i class="fa fa-circle-o"></i> Manage All </a></li>
                <li title="Manage Category"><a href="{{ url('admin/manageCategory')}}"><i class="fa fa-circle-o"></i> Manage Category </a></li>
              @endif
              @if($adminUser->hasRole('admin') || $adminUser->hasRole('sub-admin'))
                <li title="Manage Sub Category"><a href="{{ url('admin/manageSubCategory')}}"><i class="fa fa-circle-o"></i> Manage Sub Category </a></li>
                <li title="Manage Subject"><a href="{{ url('admin/manageSubject')}}"><i class="fa fa-circle-o"></i> Manage Subject </a></li>
                <li title="Manage Paper"><a href="{{ url('admin/managePaper')}}"><i class="fa fa-circle-o"></i> Manage Paper </a></li>
                <li title="Manage Question"><a href="{{ url('admin/manageQuestions')}}"><i class="fa fa-circle-o"></i> Manage Question </a></li>
                <li title="Question Bank"><a href="{{ url('admin/showQuestionBank')}}"><i class="fa fa-circle-o"></i> Question Bank </a></li>
                <li title="Upload Excel File"><a href="{{ url('admin/uploadQuestions')}}"><i class="fa fa-circle-o"></i> Upload Excel File </a></li>
              @endif
              @if($adminUser->hasRole('admin'))
                <li title="Associate Session to Question"><a href="{{ url('admin/associateSession')}}"><i class="fa fa-circle-o"></i> Associate Session </a></li>
              @endif
            </ul>
          </li>
        @if($adminUser->hasRole('admin'))
          <li class="treeview">
            <a href="#" title="Question Bank">
              <i class="fa fa-share"></i>
              <span>Question Bank</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Manage Category"><a href="{{ url('admin/manageQuestionBankCategory')}}"><i class="fa fa-circle-o"></i> Manage Category </a></li>
              <li title="Manage Sub Category"><a href="{{ url('admin/manageQuestionBankSubCategory')}}"><i class="fa fa-circle-o"></i> Manage Sub Category </a></li>
              <li title="Manage Question"><a href="{{ url('admin/manageQuestionBankQuestions')}}"><i class="fa fa-circle-o"></i> Manage Question </a></li>
              <li title="Upload Excel File"><a href="{{ url('admin/uploadQuestionBankQuestions')}}"><i class="fa fa-circle-o"></i> Upload Excel File </a></li>
            </ul>
          </li>
        @endif
        @if($adminUser->hasRole('admin'))
          <li class="treeview">
            <a href="#" title="Live Courses">
              <i class="fa fa-pie-chart"></i>
              <span>Live Courses</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Manage Live Courses"><a href="{{ url('admin/manageLiveCourse')}}"><i class="fa fa-circle-o"></i> Manage Live Courses </a></li>
              <li title="Manage Live Videos"><a href="{{ url('admin/manageLiveVideo')}}"><i class="fa fa-circle-o"></i> Manage Live Videos </a></li>
            </ul>
          </li>
        @endif
        @if($adminUser->hasRole('admin'))
          <li class="treeview">
            <a href="#" title="Documents">
              <i class="fa fa-book"></i> <span>Documents</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Manage Category"><a href="{{ url('admin/manageDocumentsCategory')}}"><i class="fa fa-circle-o"></i> Manage Category </a></li>
              <li title="Manage Documents"><a href="{{ url('admin/manageDocumentsDoc')}}"><i class="fa fa-circle-o"></i> Manage Documents </a></li>
            </ul>
          </li>
        @endif
        <li class="treeview">
          <a href="#" title="Vkit">
            <i class="fa fa-table"></i> <span>Vkit</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @if($adminUser->hasRole('admin'))
              <li title="Manage Category"><a href="{{ url('admin/manageVkitCategory')}}"><i class="fa fa-circle-o"></i> Manage Category </a></li>
            @endif
            @if($adminUser->hasRole('admin') || $adminUser->hasRole('sub-admin'))
              <li title="Manage Project"><a href="{{ url('admin/manageVkitProject')}}"><i class="fa fa-circle-o"></i> Manage Project </a></li>
            @endif
          </ul>
        </li>
        @if($adminUser->hasRole('admin'))
          <li class="treeview">
            <a href="#" title="Discussion">
              <i class="fa fa-comments-o"></i> <span>Discussion</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Manage Discussion Category"><a href="{{ url('admin/manageDiscussionCategory')}}"><i class="fa fa-circle-o"></i> Discussion Category </a></li>
            </ul>
          </li>
        @endif
        @if($adminUser->hasRole('admin'))
          <li class="treeview">
            <a href="#" title="Blog">
              <i class="fa fa-newspaper-o"></i> <span>Blog</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Manage Blog Category"><a href="{{ url('admin/manageBlogCategory')}}"><i class="fa fa-circle-o"></i> Manage Blog Category </a></li>
              <li title="Manage Blog"><a href="{{ url('admin/manageBlog')}}"><i class="fa fa-circle-o"></i> Manage Blog </a></li>
            </ul>
          </li>
        @endif
        @if($adminUser->hasRole('admin'))
          <li class="treeview">
            <a href="#" title="Sub-Admin">
              <i class="fa fa-user"></i> <span>Sub-Admin</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Manage Sub Admin"><a href="{{ url('admin/manageSubadminUser')}}"><i class="fa fa-circle-o"></i> Manage Sub Admin </a></li>
              <li title="Sub Admin Courses"><a href="{{ url('admin/manageSubadminCourses')}}"><i class="fa fa-circle-o"></i> Sub Admin Courses</a></li>
              <li title="Sub Admin Tests"><a href="{{ url('admin/manageSubadminSubCategories')}}"><i class="fa fa-circle-o"></i> Sub Admin Tests</a></li>
              <li title="Sub Admin Vkits"><a href="{{ url('admin/manageSubadminProjects')}}"><i class="fa fa-circle-o"></i> Sub Admin Vkits</a></li>
              <li title="Sub Admin Subjects"><a href="{{ url('admin/manageSubadminSubjects')}}"><i class="fa fa-circle-o"></i> Sub Admin Subjects</a></li>
            </ul>
          </li>
          <li class="treeview">
            <a href="#" title="Send-Mails">
              <i class="fa fa-envelope"></i> <span>Send-Mails</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Manage Send Mails"><a href="{{ url('admin/manageSendMails')}}"><i class="fa fa-circle-o"></i> Manage Send Mails </a></li>
            </ul>
          </li>
          <li class="treeview">
            <a href="#" title="Add College Info">
              <i class="fa fa-university"></i> <span>Add College Info</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Manage College info"><a href="{{ url('admin/manageCollegeInfo')}}"><i class="fa fa-circle-o"></i> Manage College Info </a></li>
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
              <li title="Show Users Info"><a href="{{ url('admin/allUsers')}}"><i class="fa fa-circle-o"></i> All Users </a></li>
              <li title="Show User Test Results"><a href="{{ url('admin/userTestResults')}}"><i class="fa fa-circle-o"></i> User Test Results </a></li>
              <li title="Show User Courses"><a href="{{ url('admin/userCourses')}}"><i class="fa fa-circle-o"></i> User Courses </a></li>
              <li title="Show User Placement"><a href="{{ url('admin/collegePlacement')}}"><i class="fa fa-circle-o"></i> User Placement </a></li>
              <li title="Show User video"><a href="{{ url('admin/userVideo')}}"><i class="fa fa-circle-o"></i> User Video </a></li>
              <li title="Show Un Approve Users"><a href="{{ url('admin/unapproveUsers')}}"><i class="fa fa-circle-o"></i> Un Approve Users </a></li>
              <li title="Manage Skills"><a href="{{ url('admin/manageSkill')}}"><i class="fa fa-circle-o"></i> Manage Skills </a></li>
              <li title="User Data"><a href="{{ url('admin/manageUserData')}}"><i class="fa fa-circle-o"></i> Manage User Data </a></li>
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
              <li title="Show All Test Results"><a href="{{ url('admin/allTestResults')}}"><i class="fa fa-circle-o"></i> All Test Results </a></li>
            </ul>
          </li>
          <li class="treeview">
            <a href="#" title="Subdomains/Clients Info">
              <i class="fa fa-bookmark-o"></i> <span>Clients Info</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Manage Subdomains/Clients"><a href="{{ url('admin/manageClients')}}"><i class="fa fa-circle-o"></i> Manage Clients </a></li>
              <li title="Clients Activity"><a href="{{ url('admin/clientsActivity')}}"><i class="fa fa-circle-o"></i> Clients Activity</a></li>
            </ul>
          </li>
          @endif
          <li class="treeview">
            <a href="#" title="Payments">
              <i class="fa fa-inr"></i> <span>Payments</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              @if($adminUser->hasRole('admin'))
                <li title="Payment Receipt"><a href="{{ url('admin/manageReceipt')}}"><i class="fa fa-circle-o"></i> Payment Receipt </a></li>
                <li title="Manage History"><a href="{{ url('admin/manageClientHistory')}}"><i class="fa fa-circle-o"></i> History </a></li>
                <li title="Paid Sms For Client"><a href="{{ url('admin/manageClientPaidSms')}}"><i class="fa fa-circle-o"></i> Paid Sms For Client </a></li>
                <li title="Web Development"><a href="{{ url('admin/manageWebDevelopments')}}"><i class="fa fa-circle-o"></i> Web Development </a></li>
              @endif
              @if($adminUser->hasRole('admin') || $adminUser->hasRole('sub-admin'))
                <li title="Admin Payments"><a href="{{ url('admin/manageAdminPayments')}}"><i class="fa fa-circle-o"></i> Admin Payments </a></li>
              @endif
            </ul>
          </li>
          @if($adminUser->hasRole('admin'))
          <li class="treeview">
            <a href="#" title="Zero to Hero">
              <i class="fa fa-asterisk"></i> <span>Zero to Hero</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Manage Designation"><a href="{{ url('admin/manageDesignation')}}"><i class="fa fa-circle-o"></i> Manage Designation </a></li>
              <li title="Manage Area"><a href="{{ url('admin/manageArea')}}"><i class="fa fa-circle-o"></i> Manage Area </a></li>
              <li title="Manage Zero To Hero"><a href="{{ url('admin/manageZeroToHero')}}"><i class="fa fa-circle-o"></i> Manage Zero To Hero </a></li>
            </ul>
          </li>
          <li class="treeview">
            <a href="#" title="Placement">
              <i class="fa fa-gift"></i> <span>Placement</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Placement Area"><a href="{{ url('admin/managePlacementArea')}}"><i class="fa fa-circle-o"></i> Placement Area </a></li>
              <li title="Placement Company"><a href="{{ url('admin/managePlacementCompany')}}"><i class="fa fa-circle-o"></i> Placement Company </a></li>
              <li title="Placement Company Details"><a href="{{ url('admin/managePlacementCompanyDetails')}}"><i class="fa fa-circle-o"></i> Company Details</a></li>
              <li title="Placement Process"><a href="{{ url('admin/managePlacementProcess')}}"><i class="fa fa-circle-o"></i> Placement Process</a></li>
              <li title="Placement Faq"><a href="{{ url('admin/managePlacementFaq')}}"><i class="fa fa-circle-o"></i> Placement Faq</a></li>
              <li title="Apply Job"><a href="{{ url('admin/manageApplyJob')}}"><i class="fa fa-circle-o"></i> Apply Job</a></li>
            </ul>
          </li>
          <li class="treeview">
            <a href="#" title="Workshop">
              <i class="fa fa-link"></i> <span>Workshop</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Workshop Category"><a href="{{ url('admin/manageWorkshopCategory')}}"><i class="fa fa-circle-o"></i> Workshop Category </a></li>
              <li title="Workshop Details"><a href="{{ url('admin/manageWorkshopDetails')}}"><i class="fa fa-circle-o"></i> Workshop Details </a></li>
              <li title="Workshop Videos"><a href="{{ url('admin/manageWorkshopVideos')}}"><i class="fa fa-circle-o"></i> Workshop Videos </a></li>
            </ul>
          </li>
          <li class="treeview">
            <a href="#" title="Offline Workshop">
              <i class="fa fa-space-shuttle"></i> <span>Offline Workshop</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Offline Workshop Category"><a href="{{ url('admin/manageOfflineWorkshopCategory')}}"><i class="fa fa-circle-o"></i> Workshop Category </a></li>
              <li title="Offline Workshop Details"><a href="{{ url('admin/manageOfflineWorkshopDetails')}}"><i class="fa fa-circle-o"></i> Workshop Details </a></li>
            </ul>
          </li>
          <li class="treeview">
            <a href="#" title="Motivational Speech">
              <i class="fa fa-microphone"></i><span>Motivational Speech</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Motivational Speech Speaker"><a href="{{ url('admin/manageMotivationalSpeechCategory')}}"><i class="fa fa-circle-o"></i> Motivational Speaker </a></li>
              <li title="Motivational Speech Details"><a href="{{ url('admin/manageMotivationalSpeechDetails')}}"><i class="fa fa-circle-o"></i> Motivational Details </a></li>
              <li title="Motivational Videos"><a href="{{ url('admin/manageMotivationalSpeechVideos')}}"><i class="fa fa-circle-o"></i> Motivational Videos </a></li>
            </ul>
          </li>
          <li class="treeview">
            <a href="#" title="Virtual Placement Drive">
              <i class="fa fa-handshake-o"></i><span>Virtual Placement</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Virtual Placement Drive"><a href="{{ url('admin/manageVirtualPlacementDrive')}}"><i class="fa fa-circle-o"></i> Virtual Placement </a></li>
            </ul>
          </li>
          @endif
          @if($adminUser->hasRole('admin') || $adminUser->hasRole('sub-admin'))
          <li class="treeview">
            <a href="#" title="Advertisement">
              <i class="fa fa-television"></i><span>Advertisement</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              @if($adminUser->hasRole('admin'))
                <li title="Advertisement Pages"><a href="{{ url('admin/manageAdvertisementPages')}}"><i class="fa fa-circle-o"></i> Advertisement Pages </a></li>
              @endif
              <li title="Advertisements"><a href="{{ url('admin/manageAdvertisements')}}"><i class="fa fa-circle-o"></i> Advertisements </a></li>
            </ul>
          </li>
          @endif
          @if($adminUser->hasRole('admin'))
          <li class="treeview">
            <a href="#" title="Payable Test">
              <i class="fa fa-credit-card-alt"></i>
              <span>Payable Test</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Manage Sub Category"><a href="{{ url('admin/managePayableSubCategory')}}"><i class="fa fa-circle-o"></i> Manage Sub Category </a></li>
              <li title="Manage Subject"><a href="{{ url('admin/managePayableSubject')}}"><i class="fa fa-circle-o"></i> Manage Subject </a></li>
              <li title="Manage Paper"><a href="{{ url('admin/managePayablePaper')}}"><i class="fa fa-circle-o"></i> Manage Paper </a></li>
              <li title="Manage Question"><a href="{{ url('admin/managePayableQuestions')}}"><i class="fa fa-circle-o"></i> Manage Question </a></li>
              <li title="Upload Excel File"><a href="{{ url('admin/uploadPayableQuestions')}}"><i class="fa fa-circle-o"></i> Upload Excel File </a></li>
            </ul>
          </li>
        @endif
        @if($adminUser->hasRole('admin') || $adminUser->hasRole('sub-admin'))
          <li class="treeview">
            <a href="#" title="Study Material">
              <i class="fa fa-file-pdf-o"></i> <span>Study Material</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Manage Subject"><a href="{{ url('admin/manageStudyMaterialSubject')}}"><i class="fa fa-circle-o"></i> Manage Subject </a></li>
              <li title="Manage Topic"><a href="{{ url('admin/manageStudyMaterialTopic')}}"><i class="fa fa-circle-o"></i> Manage Topic </a></li>
              <li title="Manage Post"><a href="{{ url('admin/manageStudyMaterialPost')}}"><i class="fa fa-circle-o"></i> Manage Post </a></li>
            </ul>
          </li>
        @endif
        @if($adminUser->hasRole('admin'))
          <li class="treeview">
            <a href="#" title="Mentor">
              <i class="fa fa-graduation-cap"></i> <span>Mentor</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li title="Mentor Area"><a href="{{ url('admin/manageMentorArea')}}"><i class="fa fa-circle-o"></i> Mentor Area </a></li>
              <li title="Mentor Skill"><a href="{{ url('admin/manageMentorSkill')}}"><i class="fa fa-circle-o"></i> Mentor Skill </a></li>
              <li title="MentorS"><a href="{{ url('admin/manageMentors')}}"><i class="fa fa-circle-o"></i> Mentors </a></li>
            </ul>
          </li>
        @endif
        <li class="header">LABELS</li>
        <li>
          <a onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Logout">
            <i class="fa fa-sign-out" aria-hidden="true"></i><span>Logout {{ucfirst($adminUser->name)}}</span>
            <span class="pull-right-container"></span>
          </a>
          <form id="logout-form" action="{{ url('admin/logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
          </form>
        </li>
      </ul>
    </section>
  </aside>
  <div class="content-wrapper">
    @yield('module_title')
    <div class="content">
      <div class="row">
          @yield('admin_content')
      </div>
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
