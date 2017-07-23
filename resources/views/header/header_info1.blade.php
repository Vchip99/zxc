<header>
  <section id="mu-background">
    <div class="mu-background-single">
      <div class="mu-background-img">
        <figure>
          <img src="images/header.jpg" alt="Background" style="vertical-align:top; background-attachment:fixed"/>
          <div class="container">
            <div class="row">
              @if(!Auth::user())
                <div class="col-sm-6">
                  <div class="info">
                    <p>Vchip design provide IoT base end to end solution for business development in Education sector, Health Sector, Food Sector and Agriculture Sector.We helps our clients to design, develop and deploy products and solutions for the connected world</p>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="login-wrap">
                    <div class="login-html">
                      <input id="tab-1" type="radio" name="tab" class="sign-in" checked><label for="tab-1" class="tab">Sign In</label>
                      <input id="tab-2" type="radio" name="tab" class="sign-up"><label for="tab-2" class="tab">Sign Up</label>
                      <div class="login-form">
                        <div class="sign-in-htm">
                          <form id="login-form" method="post" class="form-signin" role="form" action="{{ url('login') }}">
                          {!! csrf_field() !!}
                            <div class="group">
                              <label for="email" class="label">Username</label>
                              <input id="email" name="email" type="text" class="input" placeholder="email address" autocomplete="off" autofocus required >
                            </div>
                            <div class="group">
                              <label for="password" class="label">Password</label>
                              <input id="password" name="password" type="password" class="input" data-type="password" placeholder="password" required >
                            </div>
                            <div class="group">
                              <input id="check" type="checkbox" class="check" checked>
                              <label for="check"><span class="icon"></span> Keep me Signed in</label>
                            </div>
                            <div class="group">
                              <input type="submit" class="button" value="Sign In">
                            </div>
                            <div class="hr"></div>
                            <div class="foot-lnk">
                              <a href="#forgot">Forgot Password?</a>
                            </div>
                          </form>
                        </div>
                        <div class="sign-up-htm">
                          <form method="post" action="{{ url('register')}}" class="form-register" role="form" id="register-form">
                            {{ csrf_field() }}
                            <div class="group">
                              <label for="name" class="label">Username</label>
                              <input id="name" name="name" type="text" class="input" required>
                            </div>
                            <div class="group">
                              <label for="email" class="label">Email Address</label>
                              <input id="email" name="email" type="text" class="input" required>
                            </div>
                            <div class="group">
                              <label for="password" class="label">Password</label>
                              <input id="password" name="password" type="password" class="input" data-type="password" required>
                            </div>
                            <div class="group">
                              <label for="confirm_password" class="label">Confirm Password</label>
                              <input id="confirm_password" name="confirm_password" type="password" class="input" data-type="password" required>
                            </div>
                            <div class="group">
                              <input type="submit" class="button" value="Sign Up">
                            </div>
                            <div class="hr"></div>
                            <div class="foot-lnk">
                              <a for="tab-1">Already Member?</a>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @else
                <div class="info">
                  <p>Vchip design provide IoT base end to end solution for business development in Education sector, Health Sector, Food Sector and Agriculture Sector.We helps our clients to design, develop and deploy products and solutions for the connected world</p>
                </div>
              @endif
            </div>
          </div>
          <a href="#aboutus"><div class="bounce"><i class="arrow fa fa-angle-double-down"></i></div></a>
        </figure>
      </div>
    </div>
  </section>
</header>