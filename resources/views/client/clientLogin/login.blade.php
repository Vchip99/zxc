<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Client Admin Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<style type="text/css">
<!--
body {
  margin-left: 0px;
  margin-top: 0px;
}
-->

</style>

<div id="main">
<table border="0" width="100%" cellspacing="0" cellpadding="0" background="{{asset('images/topbkg.jpg')}}">
  <tr>
    <td width="90%" valign="top" align="left">Digital Education</td>
    <td width="10%">
     <img border="0" src="{{asset('images/topright.jpg')}}" width="203" height="68" align="right"></td>
  </tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#000000" background="{{asset('images/blackbar.jpg')}}">
  <tr>
    <td width="100%"><img border="0" src="{{asset('images/blackbar.jpg')}}" width="89" height="15"></td>
  </tr>
</table>
  <table width="100%">
  <tr>
    <td aling=right>
  </td>
  </tr>
</table>
</div>
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
<form name="form1" method="post" action="{{url('client/login')}}">
{{csrf_field()}}
<table width="490" border="0">
  <tr>
  	<Td colspan="2"></Td>
  </tr>
  <tr>
    <td width="106"><span class="style2"></span></td>
    <td width="132"><span class="style2"><span class="head1"><img src="{{asset('images/login.jpg')}}" width="131" height="155"></span></span></td>
    <td width="238"><table width="219" border="0" align="center">
  <tr>
    <td width="163" class="style2">Login ID </td>
    <td width="149"><input name="email" type="text" id="email" required></td>
  </tr>
  <tr>
    <td class="style2">Password</td>
    <td><input name="password" type="password" id="password" required></td>
  </tr>
  <tr>
    <td class="style2">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="style2">&nbsp;</td>
    <td title="Login"><input name="submit" type="submit" id="submit" value="Login"></td>
  </tr>
</table></td>
  </tr>
</table>

</form>

</body>
</html>
