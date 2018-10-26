@extends('dashboard.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Profile </h1>
  </section>
  <style type="text/css">
    @media screen and (max-width: 500px) {
      .v-container .container, .list-group .list-group-item, .col-xs-12, .col-xs-9,.col-md-offset-2, .col-xs-10{
        padding-left: 0px !important;
        padding-right: 0px !important;
      }
    }
  </style>
@stop
@section('dashboard_content')
	<div class="content-wrapper v-container tab-content" >
    <div id="profile" class="tab-pane active">
      <div class="container">
        <div class="row">
          <div class="col-md-7 col-md-offset-2">
            <div class="panel panel-default">
              <div class="panel-heading">
                @if(Session::has('message'))
                  <div class="alert alert-success" id="message">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      {{ Session::get('message') }}
                  </div>
                @endif
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
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button class="close" data-dismiss="modal">×</button>
                          <h2  class="modal-title">Edit Profile</h2>
                        </div>
                        <div class="modal-body">
                          <div class="">
                            <form action="{{url('college/'.Session::get('college_user_url').'/updateProfile')}}" method="POST" enctype="multipart/form-data">
                              {{ method_field('PUT') }}
                              {{ csrf_field() }}
                              <fieldset>
                                <div class="form-group row">
                                  <label>Name:</label>
                                  <input class="form-control" placeholder="name" name="name" type="text" value="{{$loginUser->name}}" required>
                                </div>
                                @if(2 == $loginUser->user_type && 'other' == $loginUser->college_id)
                                  <div class="form-group @if ($errors->has('college')) has-error @endif">
                                    <label>College:</label>
                                      <select class="form-control  slt mrgn_20_top" id="clg" name="college" onChange="getDepartment(this);" required>
                                        <option value="">Select College Name</option>
                                        <option value="other" id="other" @if('other' == $loginUser->college_id) selected @endif >Other</option>
                                        @if(count($colleges) > 0)
                                          @foreach($colleges as $college)
                                            <option value="{{$college->id}}" @if($college->id == $loginUser->college_id) selected @endif >{{$college->name}}</option>
                                          @endforeach
                                        @endif
                                      </select>
                                      <p class="help-block hide" id="college_error" style="color: red;">Please select college.</p>
                                  </div>
                                  <div class="form-group @if ($errors->has('department')) has-error @endif" id="deptDiv" >
                                    <label>Department:</label>
                                    <select class="form-control  slt mrgn_20_top" name="department" id="dept" required>
                                      <option value="">Select Department</option>
                                      @if(count($collegeDepts) > 0)
                                        @foreach($collegeDepts as $collegeDept)
                                          <option value="{{$collegeDept->id}}" @if($collegeDept->id == $loginUser->college_dept_id) selected @endif >{{$collegeDept->name}}</option>
                                        @endforeach
                                      @endif
                                    </select>
                                    <p class="help-block hide" id="department_error" style="color: red;">Please select department.</p>
                                  </div>
                                @endif
                                @if(2 == $loginUser->user_type)
                                  <div class="form-group @if ($errors->has('year')) has-error @endif " id="year" >
                                    <label>Year:</label>
                                    <select class="form-control  slt mrgn_20_top" name="year" required>
                                      <option value="">Select Year</option>
                                      <option value="1" @if(1 == $loginUser->year) selected @endif >First Year</option>
                                      <option value="2" @if(2 == $loginUser->year) selected @endif >Second Year </option>
                                      <option value="3" @if(3 == $loginUser->year) selected @endif >Third Year</option>
                                      <option value="4" @if(4 == $loginUser->year) selected @endif >Final Year</option>
                                    </select>
                                    <p class="help-block hide" id="year_error" style="color: red;">Please select year.</p>
                                  </div>
                                  <div class="form-group mrgn_20_top @if ($errors->has('rollno')) has-error @endif " id="rollNo">
                                    <label>Roll No:</label>
                                    <input type="number" class="form-control" name="roll_no" id="roll" value="{{$loginUser->roll_no}}" placeholder="Roll No."  min="0" />
                                    <span class="help-block"></span>
                                    <p class="help-block hide" id="roll_error" style="color: red;">Please select roll no.</p>
                                  </div>
                                  <div class="hide form-group mrgn_20_top" id="other_source">
                                    <label>Other Source:</label>
                                    <input type="text" class="form-control" name="other_source" id="other_source_input" value="" placeholder="college/company name" />
                                    <p class="help-block hide" id="other_source_error" style="color: red;">Please enter college/company name.</p>
                                  </div>
                                @endif
                                <div class="form-group">
                                  <label>Photo:</label>
                                  <input class="form-control" placeholder="Mobile No." name="photo" type="file">
                                  <label>Existing Photo:</label> {{basename($loginUser->photo)}}
                                </div>
                                @if(2 == $loginUser->user_type)
                                <div class="form-group">
                                  <label>Resume:</label>
                                  <input class="form-control" placeholder="Mobile No." name="resume" type="file">
                                  <label>Existing Resume:</label> {{basename($loginUser->resume)}}
                                </div>
                                @endif
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
                    @if(is_file($loginUser->photo) || (!empty($loginUser->photo) && false == preg_match('/userStorage/',$loginUser->photo)))
                      <img alt="User Pic" style="max-height: 200px !important;" src="{{ url($loginUser->photo)}}" id="profile-image1" class="user-prof img-responsive">
                    @else
                      <img alt="User Pic" src="{{ url('images/user/user1.png')}}" id="profile-image1" class="img-circle img-responsive">
                    @endif
                  </div>
              </div>
              <ul class="list-group">
                @if(2 == $loginUser->user_type)
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-3 "><b>Score</b></div>
                        <div class="col-xs-9 pull-left">{{$obtainedScore}}/{{$totalScore}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                @endif
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-3 "><b>Name</b></div>
                        <div class="col-xs-9 pull-left">{{$loginUser->name}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-2" data-toggle="detail-2">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-3 "><b>Email</b></div>
                        <!-- <div class="col-xs-9 pull-left">{{$loginUser->email}}</div> -->
                        <div class="col-xs-9 pull-left">
                          @if(!empty($loginUser->email))
                            {{$loginUser->email}}
                            @if($loginUser->id.'@gmail.com' == $loginUser->email)
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
                                              <label>Email-id:</label>
                                              <input class="form-control" type="email" name="email" placeholder="Enter Email-id" required/>
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
                            @else
                              @if(0 == $loginUser->verified && filter_var($loginUser->email, FILTER_VALIDATE_EMAIL))
                                <a style="float: right;" onclick="sendVerifyEmail();">Please Verify &nbsp; </a>
                                <a href="#updateEmail" data-toggle="modal" style="float: right;">Update Email &nbsp; | &nbsp;</a>
                              @endif
                              <div>
                                <form action="{{url('verifyEmail')}}" method="POST" enctype="multipart/form-data" id="verifyEmail">
                                  {{ csrf_field() }}
                                  <input class="form-control hide" type="email" name="email" value="{{$loginUser->email}}" readonly required/>
                                </form>
                              </div>
                              <div id="updateEmail" class="modal fade" role="dialog">
                                <div class="modal-dialog modal-sm">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <button class="close" data-dismiss="modal">×</button>
                                      <h2  class="modal-title">Update Email</h2>
                                    </div>
                                    <div class="modal-body">
                                      <div class="">
                                        <form action="{{url('updateEmail')}}" method="POST" enctype="multipart/form-data">
                                          {{ csrf_field() }}
                                          <fieldset>
                                            <div class="form-group">
                                              <label>Email-id:</label>
                                              <input class="form-control" type="email" name="email" value="" required/>
                                            </div>
                                            <button class="btn btn-info" type="submit">Send</button>
                                          </fieldset>
                                        </form>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            @endif
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
                        <div class="col-xs-3 "><b>Phone</b></div>
                        <!-- <div class="col-xs-9 pull-left">{{$loginUser->phone}}</div> -->
                        <div class="col-xs-9 pull-left">
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
                                              <label class="hide" id="verifyOtpMessage">Otp sent successfully.</label>
                                            </div>
                                            <button data-dismiss="modal" class="btn btn-info" type="button">Cancel</button>
                                            <button class="btn btn-info hide" type="submit" id="verifySubmit">Submit</button>
                                            <button title="Send Otp" id="verifyOtpBtn" class="btn btn-info" onclick="event.preventDefault(); verifyVchipUserOtp();" >Send OTP</button></br>
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
                                          <label class="hide" id="addOtpMessage">Otp sent successfully.</label>
                                        </div>
                                        <button data-dismiss="modal" class="btn btn-info" type="button">Cancel</button>
                                        <button class="btn btn-info hide" type="submit" id="submit">Submit</button>
                                        <button title="Send Otp" id="sendOtpBtn" class="btn btn-info" onclick="event.preventDefault(); sendVchipUserOtp();" >Send OTP</button></br>
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
                         <div class="col-xs-3 "><b> Designation</b></div>
                         <div class="col-xs-9 ">{{$users[$loginUser->user_type]}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                @if($loginUser->college_id > 0)
                  <li class="list-group-item">
                    <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                      <div class="col-xs-12">
                        <div class="row">
                           <div class="col-xs-3 "><b> College</b></div>
                           <div class="col-xs-9 ">{{$loginUser->college->name}}</div>
                        </div>
                      </div>
                    </div>
                  </li>
                  @if($loginUser->college_dept_id > 0)
                    <li class="list-group-item">
                      <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                        <div class="col-xs-12">
                          <div class="row">
                             <div class="col-xs-3 "><b> Department</b></div>
                             <div class="col-xs-9 ">{{$loginUser->department->name}}
                             @if(count($otherDepts) > 0)
                              ,{{ implode(',',$otherDepts) }}
                             @endif
                             </div>
                          </div>
                        </div>
                      </div>
                    </li>
                  @endif
                @elseif('other' == $loginUser->college_id)
                  <li class="list-group-item">
                    <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                      <div class="col-xs-12">
                        <div class="row">
                           <div class="col-xs-3 "><b> Other</b></div>
                           <div class="col-xs-9 ">{{$loginUser->other_source}}</div>
                        </div>
                      </div>
                    </div>
                  </li>
                @endif
                @if(2 == $loginUser->user_type)
                  <li class="list-group-item">
                    <div class="row toggle" id="dropdown-detail-3" data-toggle="detail-3">
                      <div class="col-xs-12">
                        <div class="row">
                           <div class="col-xs-3 "><b>Year</b></div>
                           <div class="col-xs-9 pull-left">{{$loginUser->year}}</div>
                        </div>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item">
                    <div class="row toggle" id="dropdown-detail-3" data-toggle="detail-3">
                      <div class="col-xs-12">
                        <div class="row">
                           <div class="col-xs-3 "><b>Roll No</b></div>
                           <div class="col-xs-9 pull-left">{{$loginUser->roll_no}}</div>
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
                                  <input class="form-control" type="password" name="old_password" placeholder="Enter Old Password" required/>
                                </div>
                                <div class="form-group">
                                  <label>New Password:</label>
                                  <input class="form-control" type="password" name="password" placeholder="Enter New Password" required/>
                                </div>
                                <div class="form-group">
                                  <label>Confirm New Password:</label>
                                  <input class="form-control" type="password" name="password_confirmation" placeholder="Confirm New Password" required/>
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
                @if(2 == $loginUser->user_type)
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
                @endif
              </ul>
            </div>
          </div>
        </div>
       </div>
    </div>
  </div>
  <input type="hidden" id="user_type" value="{{$loginUser->user_type}}">
<script type="text/javascript">
  function sendVchipUserOtp(){
    var mobile = $('#phone').val();
    if(mobile && 10 == mobile.length ){
      $.ajax({
        method: "POST",
        url: "{{url('sendVchipUserSignInOtp')}}",
        data: {mobile:mobile}
      })
      .done(function( result ) {
        $('#otpDiv').removeClass('hide');
        $('#sendOtpBtn').addClass('hide');
        $('#submit').removeClass('hide');
        $('#phone').prop('readonly', true);

        var resultObj = JSON.parse(result);
        if('000' == resultObj.ErrorCode && 'Success' == resultObj.ErrorMessage){
          $('#addOtpMessage').removeClass('hide');
        } else {
          $.confirm({
            title: 'Alert',
            content: 'Something wrong in otp result.'
          });
        }
      });
    } else if(!mobile) {
      alert('enter mobile no.');
    } else if(mobile.length < 10){
      alert('Enter 10 digit mobile no.');
    }
  }

  function verifyVchipUserOtp(){
    var mobile = $('#verifyPhone').val();
    if(mobile && 10 == mobile.length ){
      $.ajax({
        method: "POST",
        url: "{{url('sendVchipUserSignInOtp')}}",
        data: {mobile:mobile}
      })
      .done(function( result ) {
        $('#verifyOtpDiv').removeClass('hide');
        $('#verifyOtpBtn').addClass('hide');
        $('#verifySubmit').removeClass('hide');
        var resultObj = JSON.parse(result);
        if('000' == resultObj.ErrorCode && 'Success' == resultObj.ErrorMessage){
          $('#verifyOtpMessage').removeClass('hide');
        } else {
          $.confirm({
            title: 'Alert',
            content: 'Something wrong in otp result.'
          });
        }
      });
    } else if(!mobile) {
      alert('enter mobile no.');
    } else if(mobile.length < 10){
      alert('Enter 10 digit mobile no.');
    }
  }

  function toggleOptions(ele){
    var selectedVal = $(ele).val();
    if(3 == selectedVal || 4 == selectedVal){
      document.getElementById('year').classList.add("hide");
      document.getElementById('rollNo').classList.add("hide");
      document.getElementById('deptDiv').classList.add("show");
      document.getElementById('clg').classList.remove("hide");

      document.getElementById('deptDiv').classList.remove("hide");
      document.getElementById('year').classList.remove("show");
      document.getElementById('rollNo').classList.remove("show");
      document.getElementById('clg').classList.add("show");
    } else if(2 == selectedVal){
      document.getElementById('year').classList.add("show");
      document.getElementById('year').classList.remove("hide");

      document.getElementById('rollNo').classList.add("show");
      document.getElementById('rollNo').classList.remove("hide");
      document.getElementById('deptDiv').classList.add("show");
      document.getElementById('clg').classList.remove("hide");

      document.getElementById('deptDiv').classList.remove("hide");
      document.getElementById('clg').classList.add("show");
    } else if(5 == selectedVal || 6 == selectedVal){
      document.getElementById('year').classList.remove("show");
      document.getElementById('year').classList.add("hide");

      document.getElementById('rollNo').classList.remove("show");
      document.getElementById('rollNo').classList.add("hide");
      document.getElementById('deptDiv').classList.remove("show");
      document.getElementById('clg').classList.remove("hide");

      document.getElementById('deptDiv').classList.add("hide");
      document.getElementById('clg').classList.add("show");
    }
  }

  function getDepartment(ele){
    var college = $(ele).val();
    var userType = document.getElementById('user_type').value;
    if((2 == userType || 3 == userType || 4 == userType) && college > 0){
      $.ajax({
          method: "POST",
          url: "{{url('getDepartments')}}",
          data: {college:college}
      })
      .done(function( msg ) {
        if( msg ){
          showDeptYearRoll();
          document.getElementById('other_source').classList.add("hide");
          document.getElementById('other_source').classList.remove("show");
          select = document.getElementById('dept');
          select.innerHTML = '';
          var opt = document.createElement('option');
          opt.value = '';
          opt.innerHTML = 'Select Department';
          select.appendChild(opt);
          if( 0 < msg.length){
            $.each(msg, function(idx, obj) {
                var opt = document.createElement('option');
                opt.value = obj.id;
                opt.innerHTML = obj.name;
                select.appendChild(opt);
            });
          }
        }
      });
    } else if('other' == college){
      hideDeptYearRoll();
      document.getElementById('other_source').classList.add("show");
      document.getElementById('other_source').classList.remove("hide");
    } else {
      document.getElementById('other_source').classList.add("hide");
      document.getElementById('other_source').classList.remove("show");
    }
  }

  function showDeptYearRoll(){
    var userType = document.getElementById('user_type').value;
    if(3 == userType || 4 == userType){
      document.getElementById('year').classList.remove("show");
      document.getElementById('rollNo').classList.remove("show");
      document.getElementById('deptDiv').classList.add("show");

      document.getElementById('year').classList.add("hide");
      document.getElementById('rollNo').classList.add("hide");
      document.getElementById('deptDiv').classList.remove("hide");
    } else {
      document.getElementById('deptDiv').classList.add("show");
      document.getElementById('year').classList.add("show");
      document.getElementById('rollNo').classList.add("show");

      document.getElementById('deptDiv').classList.remove("hide");
      document.getElementById('year').classList.remove("hide");
      document.getElementById('rollNo').classList.remove("hide");
    }
  }

  function hideDeptYearRoll(){
    document.getElementById('deptDiv').classList.add("hide");
    document.getElementById('year').classList.add("hide");
    document.getElementById('rollNo').classList.add("hide");

    document.getElementById('deptDiv').classList.remove("show");
    document.getElementById('year').classList.remove("show");
    document.getElementById('rollNo').classList.remove("show");
  }

  function sendVerifyEmail(){
    document.getElementById('verifyEmail').submit();
  }
</script>
@stop