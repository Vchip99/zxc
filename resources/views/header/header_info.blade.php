<header id="vchip-header" class="vchip-cover vchip-cover-md" role="banner" style="background-image: url('{{ url('images/header.jpg')}}');background-attachment: fixed;
  background-position: center;
  background-size:cover;
  -webkit-background-size:cover;
  -moz-background-size:cover;
  -o-background-size:cover;" data-stellar-background-ratio="0.5">
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
          <div class="col-md-7 mt-text animate-box " data-animate-effect="fadeInUp">
            <h1 class="cursive-font">Digital Education</h1>
            <div class="about-videos">
              <p data-toggle="modal" data-target="#collegeModal">
                <i class="fa fa-play-circle-o" aria-hidden="true"  id="clg"></i>
                <span class="about-video-tital"><em>COLLEGE / STUDENT</em></span>
              </p>
              <p data-toggle="modal" data-target="#instituteModal" id="cotching-inst">  <i class="fa fa-play-circle-o" aria-hidden="true" ></i>
                <span class="about-video-tital"><em>COACHING INSTITUTE</em></span>
              </p>
            </div>
          </div>
          @if(!Auth::user())
          <div class="col-md-5  animated bounceInRight" data-animate-effect="fadeInRight">
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
                      <li class="active" data-toggle="tooltip" title="Sign In"><a data-toggle="tab" href="#home">Sign In</a></li>
                      <li data-toggle="tooltip" title="Sign Up"><a href="{{ url('signup') }}">Sign Up</a></li>
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
                              <input type="checkbox" name="remember" id="remember" data-toggle="tooltip" title="Remember login"> Remember login
                            </label>
                          </div>
                          <button type="submit" value="login" name="submit" class="btn btn-info btn-block" data-toggle="tooltip" title="Login">Login</button>
                          <!-- </br> -->
                        </form>
                        <br/>
                        <div class="form-group">
                            <a href="{{ url('/auth/facebook') }}" class="btn btn-info btn-block" style="color: white; background-color: #3B5998; border-color: #3B5998;"><i class="fa fa-facebook"></i> Login</a>
                        </div>
                        <div class="form-group">
                            <a href="{{ url('/auth/google') }}" class="btn btn-info btn-block" style="color: white;background-color: #DD4B39; border-color: #DD4B39;"><i class="fa fa-google"></i> Login</a>
                        </div>
                        <div>
                          <a href="{{ url('forgotPassword')}}" data-toggle="tooltip" title="Forgot Password">Forgot Password?</a>
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
  <!-- Modal collage-->
  <div class="modal fade" id="collegeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius: 0px;">
        <div class="modal-header" style=" padding: 5px 10px; font-weight: bolder; text-align: center;">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title" id="myModalLabel">College</h4>
        </div>
        <div class="modal-body" style="padding: 0px; ">
          <div class="vid"  id="collegeVideo">
          <iframe width="560" height="315" src="https://www.youtube.com/embed/nYQairlPfbA?enablejsapi=1" frameborder="0" allowfullscreen></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
 <!-- Modal private institute-->
  <div class="modal fade" id="instituteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius: 0px;">
        <div class="modal-header" style=" padding: 5px 10px; font-weight: bolder; text-align: center;">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title" id="myModalLabel">Private Institute</h4>
        </div>
        <div class="modal-body" style="padding: 0px; ">
          <div class="vid" id="instituteVideo">
            <iframe width="560" height="315" src="https://www.youtube.com/embed/tAZDiJxIRZk?enablejsapi=1" frameborder="0" allowfullscreen></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript">
  window.onclick = function(event) {
    var modelId = $(event.target).attr('id');
    if('collegeModal' == modelId){
      toggleVideo('hide', 'collegeVideo');
    } else if('instituteModal' == modelId){
      toggleVideo('hide', 'instituteVideo');
    }
  }

  function toggleVideo(state, videoId) {
    // if state == 'hide', hide. Else: show video
    var div = document.getElementById(videoId);
    if(div.getElementsByTagName("iframe").length > 0){
      var iframe = div.getElementsByTagName("iframe")[0].contentWindow;
      func = state == 'hide' ? 'pauseVideo' : 'playVideo';
      iframe.postMessage('{"event":"command","func":"' + func + '","args":""}','*');
    }
  }

  function confirmSubmit(){
    subdomain = document.getElementById('subdomain').value;
    if(subdomain){
      //Build your expression
      var regex = new RegExp("^[a-zA-Z]+[a-zA-Z0-9\\-]*$");
      //Test your current value
      if(false == regex.test(subdomain )){
        $.alert({
            title: 'Alert!',
            content: 'Please enter subdomain correctly. check given example.',
        });
      }
    }
    document.getElementById('registerUser').submit();
  }
</script>