@extends('clientuser.dashboard.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Profile </h1>
  </section>
  <style type="text/css">
    @media screen and (max-width: 320px) {
      .container, .list-group .list-group-item, .col-xs-12, .col-xs-7, .col-sm-12{
        padding-left: 0px !important;
        padding-right: 0px !important;
      }
      .col-md-offset-2, .col-xs-10, .content{
        padding-left: 5px !important;
        padding-right: 5px !important;
      }
    }

    @media screen and (min-width: 350px) and (max-width: 375px) {
      .container, .list-group .list-group-item, .col-xs-12, .col-xs-7, .col-sm-12{
        padding-left: 0px !important;
        padding-right: 0px !important;
      }
      .col-md-offset-2, .col-xs-10, .content{
        padding-left: 5px !important;
        padding-right: 5px !important;
      }
    }
  </style>
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
                @if(Session::has('message'))
                  <div class="alert alert-success" id="message">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      {{ Session::get('message') }}
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
                                  <input class="form-control" placeholder="name" name="name" type="text" value="{{$loginUser->name}}">
                                </div>
                                <div class="form-group">
                                  <label>Photo:</label>
                                  <input class="form-control" placeholder="Mobile No." name="photo" type="file">
                                  <label>Existing Photo:</label> {{basename($loginUser->photo)}}
                                </div>
                                <div class="form-group">
                                  <label>Resume:</label>
                                  <input class="form-control" placeholder="Mobile No." name="resume" type="file">
                                  <label>Existing Resume:</label> {{basename($loginUser->resume)}}
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
                    @if(!empty($loginUser->photo))
                      <img alt="User Pic" style="max-height: 200px !important;" src="{{$loginUser->photo}}" id="profile-image1" class="user-prof img-responsive">
                    @else
                      <img alt="User Pic"  src="{{ url('images/user/user1.png')}}" id="profile-image1" class="user-prof img-responsive">
                    @endif
                  </div>
              </div>
              <ul class="list-group">
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Score</b></div>
                        <div class="col-xs-7 pull-left">{{$obtainedScore}}/{{$totalScore}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Name</b></div>
                        <div class="col-xs-7 pull-left">{{$loginUser->name}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-2" data-toggle="detail-2">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Email Id/User Id</b></div>
                        <div class="col-xs-7 pull-left">
                          @if(!empty($loginUser->email))
                            {{$loginUser->email}}
                            @if(0 == $loginUser->verified && filter_var($loginUser->email, FILTER_VALIDATE_EMAIL))
                              <a href="#verifyEmail" data-toggle="modal" style="float: right;">Please Verify</a>
                              <div id="verifyEmail" class="modal fade" role="dialog">
                                <div class="modal-dialog modal-sm">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <button class="close" data-dismiss="modal">×</button>
                                      <h2  class="modal-title">Verify Email</h2>
                                    </div>
                                    <div class="modal-body">
                                      <div class="">
                                        <form action="{{url('verifyEmail')}}" method="POST" enctype="multipart/form-data">
                                          {{ csrf_field() }}
                                          <fieldset>
                                            <div class="form-group">
                                              <label>Email-id/User-id:</label>
                                              <input class="form-control" type="text" name="email" value="{{$loginUser->email}}" readonly required/>
                                            </div>
                                            <button class="btn btn-info" type="submit">Verify</button>
                                          </fieldset>
                                        </form>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            @endif
                          @else
                            <a href="#addEmail" data-toggle="modal" style="float: right;">Add Email</a>
                            <div id="addEmail" class="modal fade" role="dialog">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button class="close" data-dismiss="modal">×</button>
                                    <h2  class="modal-title">Add Email</h2>
                                  </div>
                                  <div class="modal-body">
                                    <div class="">
                                      <form action="{{url('addEmail')}}" method="POST" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <fieldset>
                                          <div class="form-group">
                                            <label>Email-id/User-id:</label>
                                            <input class="form-control" type="text" name="email" placeholder="Enter Email-id/User-id" required/>
                                          </div>
                                          <div class="form-group">
                                            <label>Password:</label>
                                            <input class="form-control" type="password" name="password" placeholder="Enter Password" required/>
                                          </div>
                                          <div class="form-group">
                                            <label>Confirm Password:</label>
                                            <input class="form-control" type="password" name="confirm_password" placeholder="Confirm Password" required/>
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
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-3" data-toggle="detail-3">
                    <div class="col-xs-12">
                      <div class="row">
                         <div class="col-xs-5 "><b>Mobile</b></div>
                         <div class="col-xs-7 pull-left">
                          @if(!empty($loginUser->phone))
                            {{$loginUser->phone}}
                            @if(0 == $loginUser->number_verified)
                              <a href="#verifyMobile" data-toggle="modal" style="float: right;">Please Verify</a>
                              <div id="verifyMobile" class="modal fade" role="dialog">
                                <div class="modal-dialog modal-sm">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <button class="close" data-dismiss="modal">×</button>
                                      <h2  class="modal-title">Verify Mobile</h2>
                                    </div>
                                    <div class="modal-body">
                                      <div class="">
                                        <form action="{{url('verifyMobile')}}" method="POST" enctype="multipart/form-data">
                                          {{ csrf_field() }}
                                          <fieldset>
                                            <div class="form-group">
                                              <label>Mobile:</label>
                                              <input type="phone" class="form-control" name="phone" id="verifyPhone" value="{{$loginUser->phone}}" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}" readonly />
                                            </div>
                                            <div class="form-group hide" id="verifyOtpDiv">
                                              <label>Otp:</label>
                                              <input name="user_otp" type="text" class="form-control" placeholder="Enter OTP" required>
                                            </div>
                                            <button data-dismiss="modal" class="btn btn-info" type="button">Cancel</button>
                                            <button class="btn btn-info hide" type="submit" id="verifySubmit">Submit</button>
                                            <button title="Send Otp" id="verifyOtpBtn" class="btn btn-info" onclick="event.preventDefault(); verifyClientUserOtp();" >Send OTP</button></br>
                                          </fieldset>
                                        </form>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            @endif
                            <a href="#changeMobile" data-toggle="modal" style="float: right;">Change&nbsp;&nbsp;&nbsp;</a>
                          @else
                            <a href="#changeMobile" data-toggle="modal" style="float: right;">Add</a>
                          @endif
                          <div id="changeMobile" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <button class="close" data-dismiss="modal">×</button>
                                  <h2  class="modal-title">Add/Change Mobile</h2>
                                </div>
                                <div class="modal-body">
                                  <div class="">
                                    <form action="{{url('updateMobile')}}" method="POST" enctype="multipart/form-data">
                                      {{ csrf_field() }}
                                      <fieldset>
                                        <div class="form-group">
                                          <label>Mobile:</label>
                                          <input type="phone" class="form-control" name="phone" id="phone" value="" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}" required/>
                                        </div>
                                        <div class="form-group hide" id="otpDiv">
                                          <label>Otp:</label>
                                          <input name="user_otp" type="text" class="form-control" placeholder="Enter OTP" required>
                                        </div>
                                        <button data-dismiss="modal" class="btn btn-info" type="button">Cancel</button>
                                        <button class="btn btn-info hide" type="submit" id="submit">Submit</button>
                                        <button title="Send Otp" id="sendOtpBtn" class="btn btn-info" onclick="event.preventDefault(); sendClientUserOtp();" >Send OTP</button></br>
                                      </fieldset>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                    <div class="col-xs-12">
                      <div class="row">
                         <div class="col-xs-5 "><b> Designation</b></div>
                         <div class="col-xs-7 ">Student</div>
                      </div>
                    </div>
                  </div>
                </li>
                @if(!empty($loginUser->email))
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
                                  <input class="form-control" type="password" name="confirm_password" placeholder="Confirm New Password" />
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
                @endif
                <li class="list-group-item" style="cursor: pointer;">
                  <div class="row toggle" id="dropdown-detail-2" data-toggle="detail-5">
                      <div class="col-xs-10">
                        <div class="row">
                            <div class="col-xs-6 ">
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
                            @if($loginUser->resume)
                              <iframe src="{{asset($loginUser->resume)}}" frameborder="0"></iframe>
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
<script type="text/javascript">
  function sendClientUserOtp(){
    var mobile = $('#phone').val();
    if(mobile){
      $.ajax({
        method: "POST",
        url: "{{url('sendClientUserOtp')}}",
        data: {mobile:mobile}
      })
      .done(function( result ) {
        $('#otpDiv').removeClass('hide');
        $('#sendOtpBtn').addClass('hide');
        $('#submit').removeClass('hide');
        $('#phone').prop('readonly', true);
      });
    } else {
      alert('enter mobile no.');
    }
  }

  function verifyClientUserOtp(){
    var mobile = $('#verifyPhone').val();
    if(mobile){
      $.ajax({
        method: "POST",
        url: "{{url('sendClientUserOtp')}}",
        data: {mobile:mobile}
      })
      .done(function( result ) {
        $('#verifyOtpDiv').removeClass('hide');
        $('#verifyOtpBtn').addClass('hide');
        $('#verifySubmit').removeClass('hide');
      });
    } else {
      alert('enter mobile no.');
    }
  }
</script>
@stop