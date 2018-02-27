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
      @if (session('status'))
          <div class="alert alert-success">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              {{ session('status') }}
          </div>
      @endif
      <div class="login-body ">
      <h2>Sign In</h2>
        <form method="POST" action="{{url('client/login')}}">
          {{csrf_field()}}
            <div class="form-group">
              <input id="email" name="email" type="text" class="form-control" placeholder="email" autocomplete="off" onfocus="this.type='email'" required>
            </div>
            <div class="form-group">
              <input id="password" name="password" type="text" class="form-control" placeholder="password" data-type="password" autocomplete="off" onfocus="this.type='password'" required >
            </div>
            <div class="form-group">
              <button id="signupSubmit" type="submit" class="btn btn-info btn-block">Login</button>
               <div class="form-group">
                  <div class="col-md-12 control">
                      <a href="{{ url('clientforgotPassword')}}" data-toggle="tooltip" title="Forgot Password">Forgot Password?</a>
                  </div>
              </div>
            </div>
        </form>
      </div>
      </div>
    </div>
   </div>
</section>
</body>
</html>