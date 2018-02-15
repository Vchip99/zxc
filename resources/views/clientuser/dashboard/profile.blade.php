@extends('clientuser.dashboard.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Profile </h1>
  </section>
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
@stop
@section('dashboard_content')
	<div class="content-wrapper v-container tab-content" >
    <div id="profile" class="tab-pane active">
      <div class="container">
        <div class="row">
          <div class="col-md-7 col-md-offset-2">
            <div class="panel panel-default">
              <div class="panel-heading">
                @if(count($errors) > 0)
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
                @endif
                <a class="btn-top pull-right"  href="#edit-all" class="btn btn-primary btn-success pull-right" data-toggle="modal" style="position: absolute;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit All</a>
                  <div id="edit-all" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-sm">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button class="close" data-dismiss="modal">×</button>
                          <h2  class="modal-title">Edit Profile</h2>
                        </div>
                        <div class="modal-body">
                          <div class="">
                            <form action="{{url('updateProfile')}}" method="POST" enctype="multipart/form-data">
                              {{ method_field('PUT') }}
                              {{ csrf_field() }}
                              <fieldset>
                                <div class="form-group">
                                  <label>Name:</label>
                                  <input class="form-control" placeholder="name" name="name" type="text" value="{{Auth::guard('clientuser')->user()->name}}">
                                </div>
                                <div class="form-group">
                                  <label>Email:</label>
                                  <input class="form-control" placeholder="yourmail@example.com" name="email" type="text" value="{{Auth::guard('clientuser')->user()->email}}">
                                </div>
                                <div class="form-group">
                                  <label>Phone:</label>
                                  <input class="form-control" placeholder="Mobile No." name="phone" type="text" value="{{Auth::guard('clientuser')->user()->phone}}">
                                </div>
                                <div class="form-group">
                                  <label>Photo:</label>
                                  <input class="form-control" placeholder="Mobile No." name="photo" type="file">
                                  <label>Existing Photo:</label> {{basename(Auth::guard('clientuser')->user()->photo)}}
                                </div>
                                <div class="form-group">
                                  <label>Resume:</label>
                                  <input class="form-control" placeholder="Mobile No." name="resume" type="file">
                                  <label>Existing Resume:</label> {{basename(Auth::guard('clientuser')->user()->resume)}}
                                </div>
                                <button data-dismiss="modal" class="btn btn-info" type="button">Cancel</button>
                                <button class="btn btn-info" type="submit">Submit</button>
                              </fieldset>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div  align="center" style="background-image: url('{{ url('images/user/profile-bg.jpg')}}');"">
                    @if(!empty(Auth::guard('clientuser')->user()->photo))
                      <img alt="User Pic" style="max-height: 200px !important;" src="{{Auth::guard('clientuser')->user()->photo}}" id="profile-image1" class="user-prof img-responsive">
                    @else
                      <img alt="User Pic"  src="{{ url('images/user/user1.png')}}" id="profile-image1" class="user-prof img-responsive">
                    @endif
                  </div>
              </div>
              <ul class="list-group">
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-10">
                      <div class="row">
                        <div class="col-xs-5 "><b>Name</b></div>
                        <div class="col-xs-7 pull-left">{{Auth::guard('clientuser')->user()->name}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-2" data-toggle="detail-2">
                    <div class="col-xs-10">
                      <div class="row">
                        <div class="col-xs-5 "><b>EMAIL</b></div>
                        <div class="col-xs-7 pull-left">{{Auth::guard('clientuser')->user()->email}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-3" data-toggle="detail-3">
                    <div class="col-xs-10">
                      <div class="row">
                         <div class="col-xs-5 "><b>PHONE</b></div>
                         <div class="col-xs-7 pull-left">{{Auth::guard('clientuser')->user()->phone}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                    <div class="col-xs-10">
                      <div class="row">
                         <div class="col-xs-5 "><b> DESIGNATION</b></div>
                         <div class="col-xs-7 ">Student</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item" style="cursor: pointer;">
                  <div class="row toggle" id="dropdown-detail-2" data-toggle="detail-5">
                      <div class="col-xs-10">
                        <div class="row">
                            <div class="col-xs-6 ">
                              <b><a href="#updatePassword" data-toggle="modal" >Update Password</a></b>
                            </div>
                        </div>
                      </div>
                      <div class="col-xs-2" title="edit"><a href="#updatePassword" data-toggle="modal" ><i class="fa fa-edit pull-right" ></i></a></div>
                  </div>
                  <div id="updatePassword" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-sm">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button class="close" data-dismiss="modal">×</button>
                          <h2  class="modal-title">Update Password</h2>
                        </div>
                        <div class="modal-body">
                          <div class="">
                            <form action="{{url('updatePassword')}}" method="POST" enctype="multipart/form-data">
                              {{ method_field('PUT') }}
                              {{ csrf_field() }}
                              <fieldset>
                                <div class="form-group">
                                  <label>Old Password:</label>
                                  <input class="form-control" type="password" name="old_password" placeholder="Enter Old Password" />
                                </div>
                                <div class="form-group">
                                  <label>New Password:</label>
                                  <input class="form-control" type="password" name="password" placeholder="Enter New Password" />
                                </div>
                                <div class="form-group">
                                  <label>Confirm New Password:</label>
                                  <input class="form-control" type="password" name="password_confirmation" placeholder="Confirm New Password" />
                                </div>
                                <button data-dismiss="modal" class="btn btn-info" type="button">Cancel</button>
                                <button class="btn btn-info" type="submit">Submit</button>
                              </fieldset>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item" style="cursor: pointer;">
                  <div class="row toggle" id="dropdown-detail-2" data-toggle="detail-5">
                      <div class="col-xs-10">
                        <div class="row">
                            <div class="col-xs-12 ">
                              <b><a href="#student_resume" data-toggle="modal" >Show Resume</a></b>
                            </div>
                        </div>
                      </div>
                      <div class="col-xs-2" title="edit"><a href="#student_resume" data-toggle="modal" ><i class="fa fa-edit pull-right" ></i></a></div>
                  </div>
                  <div id="student_resume" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button class="close" data-dismiss="modal">×</button>
                          <h2  class="modal-title">Resume</h2>
                        </div>
                        <div class="modal-body">
                          <div class="iframe-container">
                            @if(Auth::guard('clientuser')->user()->resume)
                              <iframe src="{{asset(Auth::guard('clientuser')->user()->resume)}}" frameborder="0"></iframe>
                            @else
                              Resume of Student is not uploaded
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
       </div>
    </div>
  </div>
@stop