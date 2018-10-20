 <!DOCTYPE html>
 <html lang="en">
 <head>
  <title>Register</title>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="{{asset('css/bootstrap.min.css?ver=1.0')}}" rel="stylesheet">
  <link href="{{asset('css/jquery-confirm.min.css?ver=1.0')}}" rel="stylesheet">
  <script src="{{asset('js/jquery.min.js?ver=1.0')}}"></script>
  <script src="{{asset('js/bootstrap.min.js?ver=1.0')}}"></script>
  <script src="{{asset('js/jquery-confirm.min.js?ver=1.0')}}"></script>
  <link href="{{asset('css/index.css')}}" rel="stylesheet"/>
  <link href="{{asset('css/v_main.css')}}" rel="stylesheet"/>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
  <style type="text/css">
    .fullscreen_bg {
      position: fixed;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      background-size: cover;
      background-position: 50% 50%;
      background-image: url('{{ url('images/header.jpg')}}');
      background-repeat:repeat;
    }

    .form-wrap {
    border-top: 10px solid #01bafd;
    position: relative;
    width: 100%;
  }
 .form-wrap h3 {
    font-family: "Kaushan Script", cursive;
  }
   .form-wrap .tab-content {
    z-index: 10;
    position: relative;
    clear: both;
    background: rgba(0, 0, 0, 0.5);
    padding: 30px;}
.form-wrap .tab-content h3 {
    color: #fff;  }
  .form-wrap .tab-content label {
    color: rgba(255, 255, 255, 0.8);  }
 .form-wrap .tab-content .tab-content-inner {
    display: none;  }
 .form-wrap .tab-content .tab-content-inner.active {
    display: block;  }
 .form-wrap .tab-content .form-control {
    color: #fff !important;
    border: 2px solid rgba(255, 255, 255, 0.2);  }
 option{color: #000;
  background-color:  transparent !important; }
  .v_container{margin-top:50px ;
    margin-bottom:50px ;}
    #subdomainDiv.show {
     display: table!important;
}
  </style>

</head>
<body>

  <div id="fullscreen_bg" class="fullscreen_bg"></div>
  <div class="container v_container">
    <div class="overlay"></div>
    <div class="vchip-container " >
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
          <div class="form-wrap">
            <div class="tab">
              <div class="tab-content">
                <div class="tab-content-inner active" data-content="signup">
                  <ul class=" nav-tabs v_login_reg text-center">
                    <li class="active"><a data-toggle="tab" href="#home">Sign Up</a></li>
                  </ul>
                    <div class="tab-content">
                      <div id="home" class="tab-pane fade in active">
                        <form id="registerUser" method="post" action="{{ url('register')}}">
                          {{ csrf_field() }}
                          <div class="form-group" style="color: white;">
                            <input type="radio" name="signup_type" id="signupRadioEmail" value="email" checked onClick="toggleSignUp(this.value);">Email-id
                            <input type="radio" name="signup_type" value="mobile" onClick="toggleSignUp(this.value);">Mobile
                          </div>
                          <div class="form-group @if ($errors->has('name')) has-error @endif">
                           <input id="name" type="text" class="form-control" name="name" value="{{ old('name')?:''}}" placeholder="Full Name" autocomplete="off" required="true">
                            <span class="help-block"></span>
                            @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
                          </div>
                          <div class="form-group @if ($errors->has('phone')) has-error @endif">
                            <input type="phone" class="form-control" id="signUpPhone" name="phone" value="{{ old('phone')?:''}}" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}" required="true">
                            <span class="help-block"></span>
                            @if($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
                          </div>
                          <div class="form-group @if ($errors->has('email')) has-error @endif">
                            <input id="email" name="email" type="text" class="form-control signUpEmail" value="{{ old('email')?:''}}" onfocus="this.type='email'" autocomplete="off" placeholder="Email" required="true">
                            <span class="help-block"></span>
                            @if($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
                          </div>
                          <div class="form-group @if ($errors->has('password')) has-error @endif">
                            <input id="password" name="password" type="text" class="form-control signUpEmail" data-type="password" onfocus="this.type='password'" autocomplete="off" placeholder="Password" required="true">
                            <span class="help-block"></span>
                            @if($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
                          </div>
                          <div class="form-group @if ($errors->has('confirm_password')) has-error @endif">
                            <input id="confirm_password" name="confirm_password" type="text" class="form-control signUpEmail" data-type="password" onfocus="this.type='password'" autocomplete="off" placeholder="Confirm Password" required="true">
                            <span class="help-block"></span>
                            @if($errors->has('confirm_password')) <p class="help-block">{{ $errors->first('confirm_password') }}</p> @endif
                          </div>
                          <div class="form-group @if ($errors->has('user_type')) has-error @endif">
                            <select class="form-control slt mrgn_20_top" id="user" name="user_type" onChange="toggleOptions(this);" required="true">
                              <option value="0">Select User</option>
                              <option value="2">Student</option>
                              <option value="3">Lecturer</option>
                              <option value="4">HOD</option>
                              <option value="5">Principal / Director</option>
                              <option value="6">TNP officer</option>
                            </select>
                            <p class="help-block hide" id="user_error" style="color: red;">Please select user.</p>
                          </div>
                          <div class="form-group @if ($errors->has('college')) has-error @endif">
                            <select class="hide form-control  slt mrgn_20_top" id="clg" name="college" onChange="getDepartment(this);">
                              <option value="0">Select College Name</option>
                              <option value="other" id="other">Other</option>
                              @if(count($colleges) > 0)
                                @foreach($colleges as $college)
                                  <option value="{{$college->id}}">{{$college->name}}</option>
                                @endforeach
                              @endif
                            </select>
                             <p class="help-block hide" id="college_error" style="color: red;">Please select college.</p>
                          </div>
                          <div class="form-group @if ($errors->has('department')) has-error @endif">
                            <select class="hide form-control  slt mrgn_20_top" id="dept" name="department">
                              <option value="0">Select Department</option>
                            </select>
                            <p class="help-block hide" id="department_error" style="color: red;">Please select department.</p>
                          </div>
                          <div class="form-group @if ($errors->has('year')) has-error @endif">
                            <select class="hide form-control  slt mrgn_20_top" id="year" name="year">
                              <option value="0">Select Year</option>
                              <option value="1">First Year</option>
                              <option value="2">Second Year </option>
                              <option value="3">Third Year</option>
                              <option value="4">Final Year</option>
                            </select>
                            <p class="help-block hide" id="year_error" style="color: red;">Please select year.</p>
                          </div>
                          <div class="hide form-group mrgn_20_top @if ($errors->has('rollno')) has-error @endif" id="rollNo">
                            <input type="number" class="form-control" name="roll_no" id="roll" min="1" value="" placeholder="Roll No." />
                            <span class="help-block"></span>
                            <p class="help-block hide" id="roll_error" style="color: red;">Please select roll no.</p>
                          </div>
                          <div class="hide form-group mrgn_20_top" id="other_source">
                            <input type="text" class="form-control" name="other_source" id="other_source_input" value="" placeholder="college/company name" />
                            <p class="help-block hide" id="other_source_error" style="color: red;">Please enter college/company name.</p>
                          </div>
                          <br/>
                          <label class="hide" style="color: white;" id="signUpOtpMessage">Otp sent successfully.</label>
                          <button title="Send Otp" id="sendSignUpOtpBtn" class="btn btn-info btn-block hide signUpMobile" onclick="event.preventDefault(); sendSignUpOtp();" >Send OTP</button></br>
                          <div class="form-group hide" id="signUpOtpDiv">
                            <input name="user_otp" type="text" class="form-control" placeholder="Enter OTP" >
                            <span class="help-block"></span>
                          </div>
                          <button id="registerBtn" class="btn btn-info btn-block signUpEmail" onclick="event.preventDefault(); confirmSubmit();" data-toggle="tooltip" title="Register">Register</button></br>
                          <a href="{{ url('/')}}" class="btn btn-default btn-block"> Home</a>
                          <div><a class="mrgn_10_top" href="{{ url('/')}}">Alredy Member?</a></div>
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
</div>
<script type="text/javascript">

  function confirmSubmit(){
    var error = 0;
    userType = document.getElementById('user').value;
    if(0 == userType){
      document.getElementById('user_error').classList.remove('hide');
    } else {
      document.getElementById('user_error').classList.add('hide');
    }

    if(2 == userType){
      clg = document.getElementById('clg').value;
      if(0 == clg){
        document.getElementById('college_error').classList.remove('hide');
        error++;
      } else {
        document.getElementById('college_error').classList.add('hide');
      }
      if(clg > 0){
        dept = document.getElementById('dept').value;
        if(0 == dept){
          document.getElementById('department_error').classList.remove('hide');
          error++;
        } else {
          document.getElementById('department_error').classList.add('hide');
        }
        year = document.getElementById('year').value;
        if(0 == year){
          document.getElementById('year_error').classList.remove('hide');
          error++;
        } else {
          document.getElementById('year_error').classList.add('hide');
        }
        roll = document.getElementById('roll').value;
        if(!roll.length){
          document.getElementById('roll_error').classList.remove('hide');
          error++;
        } else {
          document.getElementById('roll_error').classList.add('hide');
        }
      } else {
        document.getElementById('roll_error').classList.add('hide');
        document.getElementById('year_error').classList.add('hide');
        document.getElementById('department_error').classList.add('hide');
        otherSource = document.getElementById('other_source_input').value;
        if(false == isNaN(otherSource)){
          document.getElementById('other_source_error').classList.remove('hide');
          error++;
        } else {
          document.getElementById('other_source_error').classList.add('hide');
        }
      }
      if( 0 == error){
        document.getElementById('registerBtn').disabled = true;
        document.getElementById('registerUser').submit();
      } else {
        document.getElementById('registerBtn').disabled = false;
      }
    }

    if(3 == userType || 4 == userType){
      clg = document.getElementById('clg').value;
      if(0 == clg){
        document.getElementById('college_error').classList.remove('hide');
        error++;
      } else {
        document.getElementById('college_error').classList.add('hide');
      }
      dept = document.getElementById('dept').value;
      if(0 == dept){
        document.getElementById('department_error').classList.remove('hide');
        error++;
      } else {
        document.getElementById('department_error').classList.add('hide');
      }
      if( 0 == error){
        document.getElementById('registerBtn').disabled = true;
        document.getElementById('registerUser').submit();
      } else {
        document.getElementById('registerBtn').disabled = false;
      }
    }

    if(5 == userType || 6 == userType){
      clg = document.getElementById('clg').value;
      if(0 == clg){
        document.getElementById('college_error').classList.remove('hide');
        error++;
      } else {
        document.getElementById('college_error').classList.add('hide');
      }
      if( 0 == error){
        document.getElementById('registerBtn').disabled = true;
        document.getElementById('registerUser').submit();
      } else {
        document.getElementById('registerBtn').disabled = false;
      }
    }
  }

  function getDepartment(ele){
    var college = $(ele).val();
    var userType = document.getElementById('user').value;
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

  function hideDeptYearRoll(){
    document.getElementById('dept').classList.add("hide");
    document.getElementById('year').classList.add("hide");
    document.getElementById('rollNo').classList.add("hide");

    document.getElementById('dept').classList.remove("show");
    document.getElementById('year').classList.remove("show");
    document.getElementById('rollNo').classList.remove("show");
  }

  function showDeptYearRoll(){
    var userType = document.getElementById('user').value;
    if(3 == userType || 4 == userType){
      document.getElementById('year').classList.remove("show");
      document.getElementById('rollNo').classList.remove("show");
      document.getElementById('dept').classList.add("show");

      document.getElementById('year').classList.add("hide");
      document.getElementById('rollNo').classList.add("hide");
      document.getElementById('dept').classList.remove("hide");
    } else {
      document.getElementById('dept').classList.add("show");
      document.getElementById('year').classList.add("show");
      document.getElementById('rollNo').classList.add("show");

      document.getElementById('dept').classList.remove("hide");
      document.getElementById('year').classList.remove("hide");
      document.getElementById('rollNo').classList.remove("hide");
    }
  }

  function toggleOptions(ele){
    var selectedVal = $(ele).val();
    if(3 == selectedVal || 4 == selectedVal){
      document.getElementById('year').classList.add("hide");
      document.getElementById('rollNo').classList.add("hide");
      document.getElementById('dept').classList.add("show");
      document.getElementById('clg').classList.remove("hide");

      document.getElementById('dept').classList.remove("hide");
      document.getElementById('year').classList.remove("show");
      document.getElementById('rollNo').classList.remove("show");
      document.getElementById('clg').classList.add("show");
    } else if(2 == selectedVal){
      document.getElementById('year').classList.add("show");
      document.getElementById('rollNo').classList.add("show");
      document.getElementById('dept').classList.add("show");
      document.getElementById('clg').classList.remove("hide");

      document.getElementById('year').classList.remove("hide");
      document.getElementById('rollNo').classList.remove("hide");
      document.getElementById('dept').classList.remove("hide");
      document.getElementById('clg').classList.add("show");
    } else if(5 == selectedVal || 6 == selectedVal){
      document.getElementById('year').classList.remove("show");
      document.getElementById('rollNo').classList.remove("show");
      document.getElementById('dept').classList.remove("show");
      document.getElementById('clg').classList.remove("hide");

      document.getElementById('year').classList.add("hide");
      document.getElementById('rollNo').classList.add("hide");
      document.getElementById('dept').classList.add("hide");
      document.getElementById('clg').classList.add("show");
    }
  }

  function toggleSignUp(value){
    if('email' == value){
      $('.signUpEmail').prop('required', true);
      $('.signUpEmail').removeClass('hide');
      $('.signUpMobile').prop('required', false);
      $('.signUpMobile').addClass('hide');
      $('#signUpPhone').prop('required', false);
    } else {
      $('.signUpEmail').prop('required', false);
      $('.signUpEmail').addClass('hide');
      $('.signUpMobile').prop('required', true);
      $('.signUpMobile').removeClass('hide');
      $('#signUpPhone').prop('required', true);
    }
    $('#name').val('');
    $('#signUpPhone').val('');
    $('#email').val('');
    $('#password').val('');
    $('#confirm_password').val('');
  }

  function sendSignUpOtp(){
    var mobile = $('#signUpPhone').val();
    if(mobile && 10 == mobile.length ){
    $('#signUpOtpDiv').removeClass('hide');
    $('#registerBtn').removeClass('hide');
    $('#sendSignUpOtpBtn').addClass('hide');
    $('#signUpOtpMessage').removeClass('hide');
    $('#signUpPhone').prop('readonly', true);
    $('#name').prop('readonly', true);
      $.ajax({
        method: "POST",
        url: "{{url('sendVchipUserSignUpOtp')}}",
        data: {mobile:mobile}
      })
      .done(function( result ) {
        var resultObj = JSON.parse(result);
        if('000' == resultObj.ErrorCode && 'Success' == resultObj.ErrorMessage){
          $('#signUpOtpMessage').removeClass('hide');
        } else {
          $('#sendSignUpOtpBtn').removeClass('hide');
          $('#registerMobile').addClass('hide');
          $('#signUpOtpDiv').addClass('hide');
          $('#signUpPhone').prop('readonly', false);
          $('#name').prop('readonly', false);
          $('#signUpOtpMessage').addClass('hide');
          $.confirm({
            title: 'Alert',
            content: 'Something wrong in otp result.'
          });
        }
      });
    } else if(!mobile) {
      $.confirm({
        title: 'Alert',
        content: 'Enter mobile no.'
      });
    } else if(mobile.length < 10){
      $.confirm({
        title: 'Alert',
        content: 'Enter 10 digit mobile no.'
      });
    }
  }
</script>
</body>
</html>