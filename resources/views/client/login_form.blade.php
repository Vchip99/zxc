<header id="vchip-header" class="vchip-cover vchip-cover-md" role="banner" style="background-image: url('{{ url('images/header.jpg')}}')" data-stellar-background-ratio="0.5">
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
        @if (session('status'))
            <div class="alert alert-success">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ session('status') }}
            </div>
        @endif
          <div class="col-md-7 mt-text animate-box" data-animate-effect="fadeInUp">
            <span class="intro-text-small animated bounceInLeft">Vchip-EDU <a href="" target="_blank">vchiptech.com</a></span>
            <h1 class="cursive-font animated bounceInLeft">Client Digital Education</h1>
          </div>
          @if(!Auth::user())
          <div class="col-md-5  animated bounceInRight" data-animate-effect="fadeInRight">
            <div class="form-wrap">
              <div class="tab">
                <div class="tab-content">
                  <div class="tab-content-inner active" data-content="signup">
                    @if (count($errors) > 0)
                      <div class="alert alert-danger">
                          <ul>
                              @foreach ($errors->all() as $error)
                                  <li>{{ $error }}</li>
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
                            <input id="email" name="email" type="text" class="form-control" placeholder="vchip@gmail.com" onfocus="this.type='email'" autocomplete="off" required>
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group">
                            <input id="password" name="password" type="text" class="form-control" placeholder="password" data-type="password" onfocus="this.type='password'" autocomplete="off" required >
                            <span class="help-block"></span>
                          </div>
                          <div id="loginErrorMsg" class="alert alert-error hide">Wrong username or password</div>
                          <div class="checkbox">
                            <label>
                              <input type="checkbox" name="remember" id="remember"> Remember login
                            </label>
                          </div>
                          <button type="submit" value="login" name="submit" class="btn btn-info btn-block">Login</button>
                          </br>
                        </form>
                        <div title="Forgot Password">
                        <a href="{{ url('forgotPassword')}}" >Forgot Password?</a></div>
                      </div>
                      <div id="menu1" class="tab-pane fade">
                        <form id="registerUser" method="post" action="{{ url('register')}}">
                          {{ csrf_field() }}
                          <div class="form-group">
                            <input id="name" type="text" class="form-control" name="name" value="" placeholder="User / Institute" autocomplete="off" required/>
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group">
                            <input type="phone" class="form-control" name="phone" value="" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}"/>
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group">
                            <input id="email" name="email" type="text" class="form-control" onfocus="this.type='email'" autocomplete="off" placeholder="vchip@gmail.com" required>
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group">
                            <input id="password" name="password" type="text" class="form-control" data-type="password" onfocus="this.type='password'" autocomplete="off" placeholder="password" required>
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group">
                            <input id="confirm_password" name="confirm_password" type="text" class="form-control" data-type="password" onfocus="this.type='password'" autocomplete="off" placeholder="confirm password" required>
                            <span class="help-block"></span>
                          </div>
                          <label class="radio-inline text-left" title="Student">
                            <input  id="student-radiobtn" type="radio" name="user_type" value="0">Student
                          </label>
                          <label class="radio-inline text-center " title="Teacher">
                            <input  type="radio" id="teacher-radiobtn" name="user_type" value="1" >Teacher/Admin
                          </label>
                          <div class="show_hide input-group"  id="replyCommentT" style="display: none;">
                            <input type="text" class="form-control mrgn_10_top" id="subdomain" name="subdomain" placeholder="Enter subdomain" aria-describedby="basic-addon" required/>
                            <span class="input-group-addon" id="basic-addon">.vchipedu.com</span>
                          </div>
                          <div class="show_hide" style="display: none;">
                            <span readonly>Ex. gateexam.vchipedu.com</span></br>
                          </div>
                          </br>
                          <button name="register" class="btn btn-info btn-block" onclick="event.preventDefault(); confirmSubmit();" title="Register">Register</button></br>
                        </form>
                        <div>
                          <a data-toggle="tab" href="#home" title="Alredy Member">Alredy Member?</a>
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
</header>
<script type="text/javascript">
  function confirmSubmit(){
    subdomain = document.getElementById('subdomain').value;
    if(subdomain){
      //Build your expression
      var regex = new RegExp("^[a-zA-Z]+[a-zA-Z0-9\\-]*$");
      //Test your current value
      if(false == regex.test(subdomain )){
        alert('please enter subdomain correctly. check gievn example.');
        return false;
      }
    }
    document.getElementById('registerUser').submit();
  }
</script>