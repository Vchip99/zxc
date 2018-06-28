@if(!empty($subdomain->background_image))
<header id="vchip-header" class="vchip-cover vchip-cover-md" role="banner" style="{{ $subdomain->background_image }}" data-stellar-background-ratio="0.5">
@else
<header id="vchip-header" class="vchip-cover vchip-cover-md" role="banner" style="background-image: url('{{ url('images/header.jpg')}}');background-attachment: fixed;
  background-position: center;
  background-size:cover;
  -webkit-background-size:cover;
  -moz-background-size:cover;
  -o-background-size:cover;" data-stellar-background-ratio="0.5">

@endif
  <div class="overlay"></div>
  <div class="vchip-container ">
    <div class="row">
      <div class="col-md-12 col-md-offset-0 text-left">
        <div class="row mrgn_200_top">
        @if(Session::has('message'))
          <div class="alert alert-success" id="message">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              {{ Session::get('message') }}
          </div>
        @endif
        @if(session('status'))
            <div class="alert alert-success">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ session('status') }}
            </div>
        @endif
          <div class="col-md-7 mt-text animate-box" data-animate-effect="fadeInUp">
            <h1 class="cursive-font animated bounceInLeft">{!! $subdomain->home_content_value !!}</h1>
          </div>
          @if(!Auth::guard('clientuser')->user())
          <div class="col-md-5  animated " data-animate-effect="fadeInRight">
            <div class="form-wrap">
              <div class="tab">
                <div class="tab-content">
                  <div class="tab-content-inner active" data-content="signup">
                    @if(count($errors) > 0)
                      <div class="alert alert-danger">
                          <ul>
                              @foreach ($errors->all() as $error)
                                @if('verify_email' == $error)
                                  <li><a href="{{ url('verifyAccount')}}">Click here to resend verification email</a></li>
                                @else
                                  <li>{{ $error }}</li>
                                @endif
                              @endforeach
                          </ul>
                      </div>
                    @endif
                    <ul class=" nav-tabs v_login_reg text-center">
                      <li class="active"><a data-toggle="tab" href="#home" title="Sign In">Sign In</a></li>
                      <li><a data-toggle="tab" href="#menu1" title="Sign Up">Sign Up</a></li>
                    </ul>
                    <div class="tab-content">
                      <div id="home" class="tab-pane fade in active">
                        <form id="loginForm" method="post" action="{{ url('login') }}">
                            {!! csrf_field() !!}
                          <div class="form-group">
                            <input name="email" type="email" class="form-control" placeholder="vchip@gmail.com" autocomplete="off" required>
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group">
                            <input name="password" type="password" class="form-control" placeholder="password" data-type="password" autocomplete="off" required >
                            <span class="help-block"></span>
                          </div>
                          <div id="loginErrorMsg" class="alert alert-error hide">Wrong username or password</div>
                          <div class="checkbox">
                            <label style="color: white;">
                              <input type="checkbox" name="remember" id="remember"> Remember login
                            </label>
                          </div>
                          <button type="submit" value="login" name="submit" class="btn btn-info btn-block" title="Login">Login</button>
                          </br>
                        </form>
                        <div>
                        <div class="form-group">
                            <a href="{{ url('/auth/facebook') }}" class="btn btn-info btn-block" style="color: white; background-color: #3B5998; border-color: #3B5998;"><i class="fa fa-facebook"></i> Login</a>
                        </div>
                        <div class="form-group">
                            <a href="{{ url('/auth/google') }}" class="btn btn-info btn-block" style="color: white;background-color: #DD4B39; border-color: #DD4B39;"><i class="fa fa-google"></i> Login</a>
                        </div>
                        <a href="{{ url('forgotPassword')}}" title="Forgot Password">Forgot Password?</a></div>
                      </div>
                      <div id="menu1" class="tab-pane fade">
                        <form id="registerUser" method="post" action="{{ url('register')}}">
                          {{ csrf_field() }}
                          <div class="form-group">
                            <input id="name" type="text" class="form-control" name="name" value="" placeholder="User Name" autocomplete="off" required/>
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group">
                            <input type="phone" class="form-control" name="phone" value="" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}" required/>
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group">
                            <input name="email" type="email" class="form-control" autocomplete="off" placeholder="vchip@gmail.com" required>
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group">
                            <input name="password" type="password" class="form-control" data-type="password" autocomplete="off" placeholder="password" required>
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group">
                            <input id="confirm_password" name="confirm_password" type="password" class="form-control" data-type="password" autocomplete="off" placeholder="confirm password" required>
                            <span class="help-block"></span>
                          </div>
                          </br>
                          <button title="Register"  id="register" class="btn btn-info btn-block" onclick="event.preventDefault(); confirmSubmit();" >Register</button></br>
                        </form>
                        <div>
                          <a title="alredy member" data-toggle="tab" href="#home">Already member?</a>
                        </div>
                      </div>
                    </div>
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
<script type="text/javascript">
   $(function() {
     $('.multiselect').multiselect();
   });
  function confirmSubmit(){
    document.getElementById('register').setAttribute("disabled",true);
    document.getElementById('registerUser').submit();
  }
</script>
<style type="text/css">
#vchip-header .form-wrap .tab-content label {
    color: rgba(25, 23, 23, 0.8);
}
</style>
</header>