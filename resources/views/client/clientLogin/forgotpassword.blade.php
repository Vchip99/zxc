<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="SHORTCUT ICON" href="img/logo/vedu.png"/>
 <title>Vchip-edu - Digital Education, Online Courses & eLearning |Vchip Technology</title>
 <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
  <style type="text/css">
  body{
  background-image: url('{{ url('images/header.jpg')}}');
  background-attachment: fixed;
  background-position: center;
  background-size:cover;
  -webkit-background-size:cover;
  -moz-background-size:cover;
  -o-background-size:cover
  }
  .overlay {
    position: absolute;
    bottom: 0;
    top: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.5);
  }
.login-body{
  border-top:10px solid #01bafd;
  padding: 30px;
  background-color: rgba(0, 0, 0, 0.5);
}
.login-body h2{
 color: #fff;
 text-align: center;
 text-transform: uppercase;
}
.login-body  form{
  background-color: rgba(0, 0, 0, 0.5);
  padding: 30px;
}
.login-body form .form-control {
  background: transparent;
  color: #fff;
  font-size: 16px !important;
  width: 100%;
  border: 2px solid rgba(255, 255, 255, 0.2) !important;
  -webkit-transition: 0.5s;
  -o-transition: 0.5s;
  transition: 0.5s;
  border-radius: 0px!important
}
.login-body form .form-group input:focus{
  border: 2px solid #fff !important;
}
@media(max-width: 360px){
.login-body{
   padding: 30px 7px;
   }
  }
  @media (min-width: 619px) and (max-width: 768px){
.col-sm-offset-3 {
    margin-left: 20%;}
.col-sm-6{
  width: 70%;
  }
}
</style>
</head>
<body>
  <section style="margin-top: 100px;">
    <div class="container ">
      <div class="row">
        <div class="col-sm-6 col-sm-offset-3 ">
          <div class="login-body ">
            <h2>Reset Password</h2>
            @if (session('status'))
              <div class="alert alert-success">
                {{ session('status') }}
              </div>
            @endif
            <form method="POST" action="{{url('clientpassword/email')}}">
              {{ csrf_field() }}
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                  <input id="email" type="text" name="email" value="{{ old('email') }}" class="form-control" placeholder="E-Mail Address" onfocus="this.type='email'" autocomplete="off" required />
                   @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group">
                  <button id="signupSubmit" type="submit" class="btn btn-info btn-block"> Send Password Reset Link</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>
</html>