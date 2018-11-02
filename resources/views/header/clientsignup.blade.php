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
                        @if($plan->amount > 0)
                          <form id="registerClient" method="post" action="{{ url('doPayment')}}">
                        @else
                          <form id="registerClient" method="post" action="{{ url('freeRegister')}}">
                        @endif
                          {{ csrf_field() }}
                          <div class="form-group @if ($errors->has('name')) has-error @endif">
                           <input id="name" type="text" class="form-control" name="name" value="{{ is_object(Auth::user())?Auth::user()->name: old('name')?:''}}" placeholder="User Name" autocomplete="off" required="true">
                            <span class="help-block"></span>
                            @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
                          </div>
                          <div class="form-group @if ($errors->has('phone')) has-error @endif">
                            <input id="phone" type="phone" class="form-control" name="phone" value="{{ is_object(Auth::user())?Auth::user()->phone: old('phone')?:''}}" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}" required="true" onkeyup="isMobileNumber();">
                            <span class="help-block"></span>
                            <p class="help-block hide" id="phone_error" style="color: red;">Please enter valid mobile number.</p>
                            @if($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
                          </div>
                          <div class="form-group @if ($errors->has('email')) has-error @endif">
                            <input id="email" name="email" type="text" class="form-control" value="{{ is_object(Auth::user())?Auth::user()->email: old('email')?:''}}" onfocus="this.type='email'" autocomplete="off" placeholder="vchip@gmail.com" required="true">
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
                          <div class="input-group mrgn_20_top" id="subdomainDiv" >
                            <input type="text" class="form-control " id="subdomain" name="subdomain" placeholder="Enter subdomain" aria-describedby="basic-addon" onkeyup="checkSubdomain();" required/>
                            <span class="input-group-addon" id="basic-addon">.vchipedu.com</span>
                          </div>
                          <div class="mrgn_10_top" id="subdomainEx" >
                            <span readonly style="color: #fff;">Ex. gateexam.vchipedu.com</span></br>
                            <p class="help-block hide" id="subdomain_error" style="color: red;">Please enter subdomain.</p>
                            <p class="help-block hide" id="subdomain_exist" style="color: red;">given subdomain is already exist. please enter another subdomain.</p>
                          </div>
                          <br/>
                          @if(0 == $plan->amount && 0 == $plan->monthly_amount)
                            <div>
                                <label>Price: Rs. {{ $plan->amount }} </label>
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            </div>
                          @else
                            <div>
                                <label class="col-sm-5 col-form-label">Select Plan</label>
                                <input type="radio" name="plan_{{$plan->id}}" value="1" checked onclick="changeTotal(this)" data-id="{{$plan->id}}"><label>Rs. <span id="yearly_{{$plan->id}}">{{ $plan->amount }}</span>/Year</label>
                                <input type="radio" name="plan_{{$plan->id}}" value="0" onclick="changeTotal(this)"  data-id="{{$plan->id}}"><label>Rs. <span id="monthly_{{$plan->id}}">{{ $plan->monthly_amount }}</span>/Month</label>
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            </div>
                            <div class="form-group row">
                              <label class="col-sm-6 col-form-label">Select Month/Year</label>
                              <div class="col-sm-6">
                                <input type="number" id="duration_{{$plan->id}}" name="duration" value="1" min="1" data-id="{{$plan->id}}" onchange="showTotal(this);" required>
                              </div>
                            </div>
                            <div class="form-group row">
                              <label class="col-sm-6 col-form-label">Total</label>
                              <div class="col-sm-6">
                                <input type="text" name="total" id="total_{{$plan->id}}" value="{{$plan->amount}}" readonly="true" required>
                              </div>
                            </div>
                          @endif
                          <br/>
                          <!-- <button type="submit" value="Register" name="submit" class="btn btn-info btn-block">Register
                          </button><br /> -->
                          <button id="registerBtn" name="register" disabled="true" class="btn btn-info btn-block" onclick="event.preventDefault(); confirmSubmit();" data-toggle="tooltip" title="Register">Register</button></br>
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
  function checkSubdomain(){
    var error = 0;
    var subdomain = document.getElementById('subdomain').value;
    if(subdomain.length > 2){
      //Build your expression
        var regex = new RegExp("^[a-zA-Z]+[a-zA-Z0-9\\-]*$");
        //Test your current value
        if(false == regex.test(subdomain )){
          alert('Please enter subdomain correctly. check given example.');
          error++;
        }else {
          document.getElementById('subdomain_error').classList.add('hide');
        }
      document.getElementById('subdomain_exist').classList.add('hide');
      if( 0 == error){
        if('online' == subdomain){
          document.getElementById('subdomain_exist').classList.remove('hide');
          document.getElementById('registerBtn').disabled = true;
        } else {
          $.ajax({
              method: "POST",
              url: "{{url('isCLientExists')}}",
              data: {subdomain:subdomain}
          })
          .done(function( msg ) {
            if('true' == msg){
              document.getElementById('subdomain_exist').classList.remove('hide');
              document.getElementById('registerBtn').disabled = true;
            } else {
              document.getElementById('registerBtn').disabled = false;
            }
          });
        }
      } else {
        document.getElementById('registerBtn').disabled = true;
      }
    }
  }

  function confirmSubmit(){
    if(true == isMobileNumber()){
      document.getElementById('registerBtn').disabled = true;
      document.getElementById('registerClient').submit();
    }
    return false;
  }

  function isMobileNumber() {
    var mob = /^[1-9]{1}[0-9]{9}$/;
    var mobile = document.getElementById('phone').value;
      if( mob.test(mobile) == false) {
          document.getElementById('phone_error').classList.remove('hide');
          document.getElementById('registerBtn').disabled = true;

          document.getElementById('phone').focus();
          return false;
      } else {
        document.getElementById('phone_error').classList.add('hide');
        document.getElementById('registerBtn').disabled = false;
        return true;
      }
  }

  function showTotal(ele){
    var duration = $(ele).val();
    var plan = $(ele).data('id');
    var planType = $('input[name="plan_'+plan+'"]:checked').val();
    if(1 == planType){
      var price = document.getElementById('yearly_'+plan).innerHTML;
    } else {
      var price = document.getElementById('monthly_'+plan).innerHTML;
    }
    document.getElementById('total_'+plan).value = parseInt(price) * parseInt(duration);
  }
  function changeTotal(ele){
    var plan = $(ele).data('id');
    var planType = $(ele).val();
    if(1 == planType){
      var price = document.getElementById('yearly_'+plan).innerHTML;
    } else {
      var price = document.getElementById('monthly_'+plan).innerHTML;
    }
    var duration = document.getElementById('duration_'+plan).value;
    document.getElementById('total_'+plan).value = parseInt(price) * parseInt(duration);
  }
</script>
</body>
</html>