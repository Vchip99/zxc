<nav class=" navbar-lower navbar navbar-default  shrink navbar-fixed-top" style=" z-index: 1030; ">
  <div class="container">
      <div class="pull-left" >
        <a class="navbar-brand pull-left" href="{{ url('/')}}">
         <span>Vchip-edu</span>
        </a>
      </div>
      <div class="navbar-header pull-right">
        <button type="button" class="navbar-toggle pull-left" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <div class="pull-right dropdown " >
          @if(Auth::user())
          <a href="#" class="dropdown-toggle pull-right user_menu" data-toggle="dropdown" role="button" aria-expanded="false" title="User">
            @if(is_file(Auth::user()->photo))
              <img src="{{asset(Auth::user()->photo)}}" class="img-circle user-profile" alt="user name" aria-haspopup="true"   aria-expanded="true"/>&nbsp;
            @else
              <img src="{{ asset('images/user1.png') }}" class="img-circle user-profile" alt="user name" aria-haspopup="true"   aria-expanded="true"/>&nbsp;
            @endif
            @if(Auth::user()->userNotificationCount() > 0)
              <b style="color: red;">{{Auth::user()->userNotificationCount()}}</b>
            @endif
          </a>
            <ul class="dropdown-menu user-dropdown ">
                <li>
                  <a href="{{ url('profile')}}" data-toggle="tooltip" title="Dashbord">
                  @if(is_file(Auth::user()->photo))
                    <img src="{{asset(Auth::user()->photo)}}" class="img-circle user-profile1" alt="user name" aria-haspopup="true"   aria-expanded="true"/>&nbsp;
                  @else
                    <img src="{{ asset('images/user1.png') }}" class="img-circle user-profile1" alt="user name" aria-haspopup="true"   aria-expanded="true"/>&nbsp;
                  @endif
                  {{Auth::user()->name}}
                  </a>
                </li>
                <li role="separator" class="divider"></li>
                <li>
                  <a href="{{ url('myNotifications')}}" data-toggle="tooltip" title="My Notifications"><i class="fa fa-star" aria-hidden="true"></i> My Notifications : <b style="color: red;">{{Auth::user()->userNotificationCount()}}</b></a>
                </li>
                <li>
                  <a href="{{ url('adminMessages')}}" data-toggle="tooltip" title="Admin Notifications"><i class="fa fa-star" aria-hidden="true"></i> Admin Messages : <b style="color: red;">{{Auth::user()->adminNotificationCount()}}</b></a>
                </li>
                <li role="separator" class="divider"></li>
                <li><a href="{{ url('logout') }}" data-toggle="tooltip" title="Logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
                  <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                  </form>
                </li>
            </ul>
            @else
              <a href="#" class="dropdown-toggle pull-right user_menu" data-toggle="dropdown" role="button" aria-expanded="false" title="User"><img src="{{ asset('images/user1.png') }}" class="img-circle user-profile" alt="user name" aria-haspopup="true" aria-expanded="true"/>
              </a>
              <ul class="dropdown-menu" role="menu">
                <div class="navbar-content">
                  <li>
                    <a href="{{ url('/')}}"><i class="fa fa-tachometer" aria-hidden="true"></i> Login</a>
                  </li>
                </div>
              </ul>
          @endif
        </div>
      </div>
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="Digital Education">Digital Edu <span class="caret"></span>
              </a>
              <ul class="dropdown-menu" role="menu">
                <div class="navbar-content">
                 <li><a href="{{ url('courses')}}" title="Online Courses">Online Courses</a></li>
                 <!-- <li><a href="{{ url('liveCourse')}}" title="Live Courses">Live Courses</a></li> -->
                 <li class="divider"></li>
                 <li><a href="{{ url('workshops') }}" title="Workshop">Workshop</a></li>
                 <li class="divider"></li>
                 <li><a href="{{url('vkits')}}" title="Hobby Project">Hobby Project</a></li>
                 <li class="divider"></li>
                  <li><a href="{{url('documents')}}" title="Documents">Documents</a></li>
                </div>
              </ul>
          </li>
          <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="Test-Series">Test-Series <span class="caret"></span>
                </a>
              <ul class="dropdown-menu" role="menu">
                <div class="navbar-content">
                  <li><a class="" href="{{ url('online-tests') }}" data-toggle="tooltip" title="Online Tests"> Online Tests </a></li>
                  @if(count($testCategoriesWithQuestions) > 0)
                    @foreach($testCategoriesWithQuestions as $testCategory)
                      <li>
                        <a href="{{ url('/showTest') }}/{{ $testCategory->id }}" data-toggle="tooltip" title="{{ $testCategory->name }}">{{ $testCategory->name }}</a>
                      </li>
                    @endforeach
                  @endif
               </div>
              </ul>
          </li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="Service">Service<span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
             <div class="navbar-content">
               <li> <b style="color: #01bafd;">College</b></li>
                <li class="mrgn_10_left"><a href="{{ url('erp') }}" title="Digital edu & ERP">Digital edu & ERP</a></li>
                <li class="mrgn_10_left"><a href="{{ url('offlineworkshops') }}" title="workshops">workshops</a></li>
                <li class="mrgn_10_left"><a href="{{ url('motivationalspeech') }}" title="motivational speech">motivational speech</a></li>
                <li class="mrgn_10_left"><a href="{{ url('virtualplacementdrive') }}" title="virtual placement drive">virtual placement drive</a></li>
                <li class="divider"></li>
                <li> <b style="color: #01bafd;">Coaching Institute</b></li>
                 <li class="mrgn_10_left"><a href="{{ url('educationalPlatform') }}" title="Education Platform">Education Platform</a></li>
                 <li class="mrgn_10_left"><a href="{{ url('digitalMarketing') }}" title="Digital Marketing">Digital Marketing</a></li>
                 <li class="mrgn_10_left"><a href="{{ url('webdevelopment') }}" title="web & app development">web & app development</a></li>
                 <li class="mrgn_10_left"><a href="{{ url('pricing') }}" title="pricing">pricing</a></li>
             </div>
            </ul>
          </li>
         <li><a href="{{ url('placements') }}" title="Placement">placement</a></li>
          <li><a href="{{url('heros')}}" title="Zero To Hero">Z To H</a></li>
           <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="Discussion">Discussion <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
              <div class="navbar-content">
                <li><a href="{{url('discussion')}}" title="Discussion forum">Discussion forum</a></li>
                <!-- <li><a href="{{url('liveVideo')}}" title="Live video discussion">Live video discussion</a></li> -->
                <li><a href="{{url('blog')}}" title="Blog">Blog</a></li>
              </div>
            </ul>
          </li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="About">About <span class="caret"></span>
            </a>
            <ul class="dropdown-menu " role="menu">
              <div class="navbar-content">
               <li><a href="{{url('us')}}" title="US">US</a></li>
               <li><a href="{{url('ourpartner')}}" title="Our partners">Our partners</a></li>
               <li><a href="{{url('career')}}" title="Career">Career</a></li>
               <li><a href="{{url('contactus')}}" title="Contact us">Contact us</a></li>
             </div>
           </ul>
          </li>

        </ul>
      </div>
    </div>
</nav>
<input type="hidden" id="user_id" name="user_id" value="{{ (is_object(Auth::user())?Auth::user()->id:NULL)}}">
<script type="text/javascript">
  $(function () {
  $('.navbar-collapse ul li a:not(.dropdown-toggle)').click(function () {
          $('.navbar-toggle:visible').click();
  });
});
$('.dropdown').hover(function() {
  $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(500);
}, function() {
  $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(500);
});
</script>