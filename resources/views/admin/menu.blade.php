<div class="col-sm-3 col-md-2 affix-sidebar">
  <div class="sidebar-nav">
      <div class="navbar navbar-default" role="navigation">
          <div class="navbar-collapse collapse sidebar-navbar-collapse">
                <ul class="nav navbar-nav" id="sidenav01">
                  @php
                    $adminUser = Auth::guard('admin')->user();
                  @endphp
                  <li class="sidebar-brand"><a >Welcome {{ucfirst($adminUser->name)}}</a></li>
                  <li class="sidebar-brand"><a ></a></li>
                  @if($adminUser->hasRole('admin') || $adminUser->hasPermission('manageOnlineTest'))
                  <li class="active">
                      <a data-toggle="collapse" data-target="#online_test" data-parent="#sidenav01" class="collapsed">
                          <h4>Online Tests<br>
                          </h4>
                      </a>
                      <div class="collapse" id="online_test" style="height: 0px;">
                          <ul class="nav nav-list">
                              <li><a href="{{ url('admin/manageCategory')}}"><span> Manage Category </span></a></li>
                              <li><a href="{{ url('admin/manageSubCategory')}}"><span> Manage Sub Category </span></a></li>
                              <li><a href="{{ url('admin/manageSubject')}}"><span> Manage Subject </span></a></li>
                              <li><a href="{{ url('admin/managePaper')}}"><span> Manage Paper </span></a></li>
                              <li><a href="{{ url('admin/manageQuestions')}}"><span> Manage Question </span></a></li>
                              <!-- <li><a href="{{ url('admin/manageUsers')}}"><span> Manage User </span></a></li> -->
                          </ul>
                      </div>
                  </li>
                  @endif
                  @if($adminUser->hasRole('admin') || $adminUser->hasPermission('manageOnlineCourse'))
                  <li>
                    <a data-toggle="collapse" data-target="#online_courses" data-parent="#sidenav01" class="collapsed">
                          <h4>Online Courses<br>
                          <!-- <small>IOSDSV <span class="caret"></span></small> -->
                          </h4>
                      </a>
                      <div class="collapse" id="online_courses" style="height: 0px;">
                          <ul class="nav nav-list">
                              <li><a href="{{ url('admin/manageCourseCategory')}}"><span> Manage Category </span></a></li>
                              <li><a href="{{ url('admin/manageCourseSubCategory')}}"><span> Manage Sub Category </span></a></li>
                              <li><a href="{{ url('admin/manageCourseCourse')}}"><span> Manage Course </span></a></li>
                              <li><a href="{{ url('admin/manageCourseVideo')}}"><span> Manage Video </span></a></li>
                              <!-- <li><a href="{{ url('admin/manageUsers')}}"><span> Manage User </span></a></li> -->
                          </ul>
                      </div>
                  </li>
                  @endif
                  @if($adminUser->hasRole('admin') || $adminUser->hasPermission('manageVkit'))
                  <li>
                    <a data-toggle="collapse" data-target="#vkit" data-parent="#sidenav01" class="collapsed">
                          <h4>V-Kit<br>
                          </h4>
                      </a>
                      <div class="collapse" id="vkit" style="height: 0px;">
                          <ul class="nav nav-list">
                              <li><a href="{{ url('admin/manageVkitCategory')}}"><span> Manage Category </span></a></li>
                              <li><a href="{{ url('admin/manageVkitProject')}}"><span> Manage Project </span></a></li>
                          </ul>
                      </div>
                  </li>
                  @endif
                  @if($adminUser->hasRole('admin') || $adminUser->hasPermission('manageDocument'))
                   <li>
                    <a data-toggle="collapse" data-target="#materials" data-parent="#sidenav01" class="collapsed">
                          <h4>Documents<br>
                          </h4>
                      </a>
                      <div class="collapse" id="materials" style="height: 0px;">
                          <ul class="nav nav-list">
                              <li><a href="{{ url('admin/manageDocumentsCategory')}}"><span> Manage Category </span></a></li>
                              <li><a href="{{ url('admin/manageDocumentsDoc')}}"><span> Manage Documents </span></a></li>
                          </ul>
                      </div>
                  </li>
                  @endif
                  @if($adminUser->hasRole('admin') || $adminUser->hasPermission('manageBlog'))
                  <li>
                    <a data-toggle="collapse" data-target="#bolg" data-parent="#sidenav01" class="collapsed">
                          <h4>Blog<br>
                          </h4>
                      </a>
                      <div class="collapse" id="bolg" style="height: 0px;">
                          <ul class="nav nav-list">
                              <li><a href="{{ url('admin/manageBlog')}}"><span> Manage Blog </span></a></li>
                          </ul>
                      </div>
                  </li>
                  @endif
                  @if($adminUser->hasRole('admin') || $adminUser->hasPermission('manageLiveCourse'))
                  <li>
                    <a data-toggle="collapse" data-target="#liveCourse" data-parent="#sidenav01" class="collapsed">
                          <h4>Live Courses<br>
                          </h4>
                      </a>
                      <div class="collapse" id="liveCourse" style="height: 0px;">
                          <ul class="nav nav-list">
                              <li><a href="{{ url('admin/manageLiveCourse')}}"><span> Manage Live Courses </span></a></li>
                              <li><a href="{{ url('admin/manageLiveVideo')}}"><span> Manage Live Videos </span></a></li>
                          </ul>
                      </div>
                  </li>
                  @endif
                  @if($adminUser->hasRole('admin'))
                  <li>
                    <a data-toggle="collapse" data-target="#subadmin" data-parent="#sidenav01" class="collapsed">
                          <h4>Sub-Admin<br>
                          </h4>
                      </a>
                      <div class="collapse" id="subadmin" style="height: 0px;">
                          <ul class="nav nav-list">
                              <!-- <li><a href="{{ url('admin/manageSubadminUser')}}"><span> Manage Sub Admin User </span></a></li> -->
                              <li><a href="{{ url('admin/manageSubadminUser')}}"><span>Sub Admin User </span></a></li>
                              <!-- <li><a href="{{ url('admin/manageroleAndPermission')}}"><span> Manage Role And Permissions </span></a></li> -->
                          </ul>
                      </div>
                  </li>
                  @endif
                  <!-- <li><a href="{{ url('admin/logout')}}"><span> Logout User </span></a></li> -->
                  <li>
                      <a onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">
                        <h4>Logout {{ucfirst($adminUser->name)}}</h4>
                      </a>
                      <form id="logout-form" action="{{ url('admin/logout') }}" method="POST" style="display: none;">
                          {{ csrf_field() }}
                      </form>
                  </li>

                </ul>
          </div><!--/.nav-collapse -->
      </div>
  </div>
</div>

