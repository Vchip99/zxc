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
    border: 2px solid rgba(255, 255, 255, 0.2);
  }
  option{
    color: #000;
    background-color:  transparent !important;
  }
  div.multiple{
    color: #fff;
  }
  .v_container{
    margin-top:50px ;
    margin-bottom:50px ;
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
                    <li class="active"><a data-toggle="tab" href="#home">Mentor Sign Up</a></li>
                  </ul>
                    <div class="tab-content">
                      <div id="home" class="tab-pane fade in active">
                        <form id="registerUser" method="post" action="{{ url('registerMentor')}}" enctype="multipart/form-data">
                          {{ csrf_field() }}
                          <div class="form-group @if ($errors->has('name')) has-error @endif">
                           <input id="name" type="text" class="form-control" name="name" value="{{ old('name')?:''}}" placeholder="Full Name" autocomplete="off" required="true">
                            <span class="help-block"></span>
                            @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
                          </div>
                          <div class="form-group @if ($errors->has('mobile')) has-error @endif">
                            <input type="mobile" class="form-control" id="signUpPhone" name="mobile" value="{{ old('mobile')?:''}}" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}" required="true">
                            <span class="help-block"></span>
                            @if($errors->has('mobile')) <p class="help-block">{{ $errors->first('mobile') }}</p> @endif
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
                          <!-- <div class="form-group @if ($errors->has('photo')) has-error @endif">
                            <input id="photo" type="file" class="form-control" name="photo" value="{{ old('photo')?:''}}" placeholder="photo" autocomplete="off" required="true">
                             @if($errors->has('photo')) <p class="help-block">{{ $errors->first('photo') }}</p> @endif
                          </div> -->
                          <div class="form-group @if ($errors->has('designation')) has-error @endif">
                            <input id="designation" type="text" class="form-control" name="designation" value="{{ old('designation')?:''}}" placeholder="Designation" autocomplete="off" required="true">
                             @if($errors->has('designation')) <p class="help-block">{{ $errors->first('designation') }}</p> @endif
                          </div>
                          <div class="form-group @if ($errors->has('education')) has-error @endif">
                             <input id="education" type="text" class="form-control" name="education" value="{{ old('education')?:''}}" placeholder="Education" autocomplete="off" required="true">
                             @if($errors->has('education')) <p class="help-block">{{ $errors->first('education') }}</p> @endif
                          </div>
                          <div class="form-group @if ($errors->has('area')) has-error @endif">
                            @if(count($areaNames) > 0)
                              <select class="form-control" id="area" name="area" required placeholder="Area" onChange="selectSkill(this);">
                                <option value=""> Select Area </option>
                                @foreach($areaNames as $areaId => $areaName)
                                  <option value="{{$areaId}}"> {{$areaName}} </option>
                                @endforeach
                              </select>
                            @endif
                          </div>
                          <div class="form-group @if ($errors->has('skill')) has-error @endif">
                            <label>Select Skills:</label>
                            <div id="skillList" class="multiple">

                            </div>
                          </div>
                          <button type="submit" id="registerBtn" class="btn btn-info btn-block signUpEmail" title="Sign Up">Sign Up</button></br>
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
  function selectSkill(ele){
    var areaId = $(ele).val();
    if(areaId > 0){
      $.ajax({
        method: "POST",
        url: "{{url('getMentorSkillsByAreaId')}}",
        data: {area:areaId}
      })
      .done(function( msg ) {
        var skillList = document.getElementById('skillList');
        skillList.innerHTML = '';
        if(msg.length > 0){
          $.each(msg, function(idx,obj){
            skillList.innerHTML +=' <input type="checkbox" name="skills[]" multiple value="'+obj.id+'"> '+obj.name;
          });
        }
      });
    }
  }
</script>
</body>
</html>