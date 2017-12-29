 <!DOCTYPE html>
 <html lang="en">
 <head>
  <title>Register</title>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="{{asset('css/bootstrap.min.css?ver=1.0')}}" rel="stylesheet">
  <script src="{{asset('js/jquery.min.js?ver=1.0')}}"></script>
  <script src="{{asset('js/bootstrap.min.js?ver=1.0')}}"></script>
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
                          <div class="form-group @if ($errors->has('name')) has-error @endif">
                           <input id="name" type="text" class="form-control" name="name" value="{{ old('name')?:''}}" placeholder="User Name" autocomplete="off" required="true">
                            <span class="help-block"></span>
                            @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
                          </div>
                          <div class="form-group @if ($errors->has('phone')) has-error @endif">
                            <input type="phone" class="form-control" name="phone" value="{{ old('phone')?:''}}" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}" required="true">
                            <span class="help-block"></span>
                            @if($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
                          </div>
                          <div class="form-group @if ($errors->has('email')) has-error @endif">
                            <input id="email" name="email" type="text" class="form-control" value="{{ old('email')?:''}}" onfocus="this.type='email'" autocomplete="off" placeholder="vchip@gmail.com" required="true">
                            <span class="help-block"></span>
                            @if($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
                          </div>
                          <div class="form-group @if ($errors->has('password')) has-error @endif">
                            <input id="password" name="password" type="text" class="form-control" data-type="password" onfocus="this.type='password'" autocomplete="off" placeholder="password" required="true">
                            <span class="help-block"></span>
                            @if($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
                          </div>
                          <div class="form-group @if ($errors->has('confirm_password')) has-error @endif">
                            <input id="confirm_password" name="confirm_password" type="text" class="form-control" data-type="password" onfocus="this.type='password'" autocomplete="off" placeholder="confirm password" required="true">
                            <span class="help-block"></span>
                            @if($errors->has('confirm_password')) <p class="help-block">{{ $errors->first('confirm_password') }}</p> @endif
                          </div>
                          <div class="form-group @if ($errors->has('user_type')) has-error @endif">
                            <select class="form-control slt mrgn_20_top" id="user" name="user_type" onChange="toggleOptions(this);" required="true">
                              <option value="0">Select User</option>
                              <!-- <option value="1">Admin/Owner of Institute </option> -->
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
                              <option value="other" id="other" class="hide">Other</option>
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
                            <input type="number" class="form-control" name="roll_no" id="roll" value="" placeholder="Roll No." />
                            <span class="help-block"></span>
                            <p class="help-block hide" id="roll_error" style="color: red;">Please select roll no.</p>
                          </div>
                          <div class="hide form-group mrgn_20_top" id="other_source">
                            <input type="text" class="form-control" name="other_source" id="other_source_input" value="" placeholder="college/company name" />
                            <p class="help-block hide" id="other_source_error" style="color: red;">Please enter college/company name.</p>
                          </div>
                          <div class="hide input-group mrgn_20_top" id="subdomainDiv" >
                            <input type="text" class="form-control " id="subdomain" name="subdomain" placeholder="Enter subdomain" aria-describedby="basic-addon" required/>
                            <span class="input-group-addon" id="basic-addon">.vchipedu.com</span>
                          </div>
                          <div class="hide mrgn_10_top" id="subdomainEx" >
                            <span readonly style="color: #fff;">Ex. gateexam.vchipedu.com</span></br>
                            <p class="help-block hide" id="subdomain_error" style="color: red;">Please enter subdomain.</p>
                          </div>
                          <br/>
                          <!-- <button type="submit" value="Register" name="submit" class="btn btn-info btn-block">Register
                          </button><br /> -->
                          <button name="register" class="btn btn-info btn-block" onclick="event.preventDefault(); confirmSubmit();" data-toggle="tooltip" title="Register">Register</button></br>
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
    // if(1 == userType){
    //   subdomain = document.getElementById('subdomain').value;
    //   if(subdomain && true == isNaN(subdomain)){
    //     //Build your expression
    //     var regex = new RegExp("^[a-zA-Z]+[a-zA-Z0-9\\-]*$");
    //     //Test your current value
    //     if(false == regex.test(subdomain )){
    //       alert('Please enter subdomain correctly. check given example.');
    //       // $.alert({
    //       //     title: 'Alert!',
    //       //     content: 'Please enter subdomain correctly. check given example.',
    //       // });
    //       error++;
    //     }else {
    //       document.getElementById('subdomain_error').classList.add('hide');
    //     }
    //   } else if(1 == userType){
    //     document.getElementById('subdomain_error').classList.remove('hide');
    //     error++;
    //   } else {
    //     document.getElementById('subdomain_error').classList.add('hide');
    //   }
    //   if( 0 == error){
    //     document.getElementById('registerUser').submit();
    //   }
    // }
    if(2 == userType){
      // degree = document.getElementById('degree').value;
      // if(0 == degree){
      //   document.getElementById('degree_error').classList.remove('hide');
      //   error++;
      // } else {
      //   document.getElementById('degree_error').classList.add('hide');
      // }
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
        document.getElementById('registerUser').submit();
      }
    }

    if(3 == userType || 4 == userType){
      // degree = document.getElementById('degree').value;
      // if(0 == degree){
      //   document.getElementById('degree_error').classList.remove('hide');
      //   error++;
      // } else {
      //   document.getElementById('degree_error').classList.add('hide');
      // }
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
        document.getElementById('registerUser').submit();
      }
    }

    if(5 == userType || 6 == userType){
      // degree = document.getElementById('degree').value;
      // if(0 == degree){
      //   document.getElementById('degree_error').classList.remove('hide');
      //   error++;
      // } else {
      //   document.getElementById('degree_error').classList.add('hide');
      // }
      clg = document.getElementById('clg').value;
      if(0 == clg){
        document.getElementById('college_error').classList.remove('hide');
        error++;
      } else {
        document.getElementById('college_error').classList.add('hide');
      }
      if( 0 == error){
        document.getElementById('registerUser').submit();
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

      document.getElementById('year').classList.add("hide");
      document.getElementById('rollNo').classList.add("hide");
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
      // document.getElementById('degree').classList.remove("hide");
      document.getElementById('clg').classList.remove("hide");

      document.getElementById('dept').classList.remove("hide");
      document.getElementById('year').classList.remove("show");
      document.getElementById('rollNo').classList.remove("show");
      // document.getElementById('degree').classList.add("show");
      document.getElementById('clg').classList.add("show");
      document.getElementById('other').classList.add("hide");


      // hideSubdomain();
    } else if(2 == selectedVal){
      document.getElementById('year').classList.add("show");
      document.getElementById('rollNo').classList.add("show");
      document.getElementById('dept').classList.add("show");
      // document.getElementById('degree').classList.remove("hide");
      document.getElementById('clg').classList.remove("hide");

      document.getElementById('year').classList.remove("hide");
      document.getElementById('rollNo').classList.remove("hide");
      document.getElementById('dept').classList.remove("hide");
      // document.getElementById('degree').classList.add("show");
      document.getElementById('clg').classList.add("show");
      document.getElementById('other').classList.remove("hide");

      // hideSubdomain();
    } else if(5 == selectedVal || 6 == selectedVal){
      document.getElementById('year').classList.remove("show");
      document.getElementById('rollNo').classList.remove("show");
      document.getElementById('dept').classList.remove("show");

      // document.getElementById('degree').classList.remove("hide");
      document.getElementById('clg').classList.remove("hide");

      document.getElementById('year').classList.add("hide");
      document.getElementById('rollNo').classList.add("hide");
      document.getElementById('dept').classList.add("hide");

      // document.getElementById('degree').classList.add("show");
      document.getElementById('clg').classList.add("show");
      document.getElementById('other').classList.add("hide");

      // hideSubdomain();
    }
    // else if(1 == selectedVal){
    //   showSubdomain();

    //   document.getElementById('year').classList.add("hide");
    //   document.getElementById('rollNo').classList.add("hide");
    //   document.getElementById('dept').classList.add("hide");
    //   // document.getElementById('degree').classList.add("hide");
    //   document.getElementById('clg').classList.add("hide");

    //   document.getElementById('year').classList.remove("show");
    //   document.getElementById('rollNo').classList.remove("show");
    //   document.getElementById('dept').classList.remove("show");
    //   // document.getElementById('degree').classList.remove("show");
    //   document.getElementById('clg').classList.remove("show");
    // }
  }

  // function hideSubdomain(){
  //     document.getElementById('subdomainDiv').classList.add("hide");
  //     document.getElementById('subdomainEx').classList.add("hide");

  //     document.getElementById('subdomainDiv').classList.remove("show");
  //     document.getElementById('subdomainEx').classList.remove("show");
  // }

  // function showSubdomain(){
  //     document.getElementById('subdomainDiv').classList.add("show");
  //     document.getElementById('subdomainEx').classList.add("show");

  //     document.getElementById('subdomainDiv').classList.remove("hide");
  //     document.getElementById('subdomainEx').classList.remove("hide");
  // }

</script>
</body>
</html>