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
  <div class="alert alert-danger">
      <ul>
        @if('admin_approve' == $error)
          <li> Your account is not approve. you can contact at info@vchiptech.com to approve your account.</li>
        @elseif('verified' == $error)
          <li> Please verify your account and then login.<br/><a href="{{ url('verifyAccount')}}">Click here to resend verification email</a></li>
        @endif
      </ul>
  </div>

</body>
</html>
